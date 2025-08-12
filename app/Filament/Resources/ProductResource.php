<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Helpers\SlugHelper;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string  $pluralModelLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Tên')
                ->required()
                ->maxLength(255)
                ->live(debounce: 1000)
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!$state) return;
                    $baseSlug = \Illuminate\Support\Str::slug($state);
                    $slug = $baseSlug;
                    $i = 1;
                    while (\App\Models\Product::withoutTrashed()->where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $i++;
                    }
                    $set('slug', $slug);
                }),

            Forms\Components\TextInput::make('slug')
                ->label('Đường dẫn')
                ->required()
                ->readOnly()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Forms\Components\Select::make('type_sale')
                ->label('Dạng sản phẩm')
                ->options([
                    'sale' => 'Bán trực tiếp',
                    'auction' => 'Đấu giá',
                ])
                ->required()
                ->live(),
            Forms\Components\TextInput::make('price')
                ->label('Giá')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('stock')
                ->label('Số lượng')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('min_bid_amount')
                ->label('Giá Dưới')
                ->numeric()
                ->requiredIf('type_sale', 'auction')
                ->visible(fn($get) => $get('type_sale') === 'auction'),
            Forms\Components\TextInput::make('max_bid_amount')
                ->label('Giá Trên')
                ->numeric()
                ->requiredIf('type_sale', 'auction')
                ->rules([
                    fn($get) => function (string $attribute, $value, $fail) use ($get) {
                        if ($get('type_sale') === 'auction') {
                            $min = $get('min_bid_amount');
                            if ($min !== null && $value <= $min) {
                                $fail('Giá trên phải lớn hơn giá dưới.');
                            }
                        }
                    }
                ])
                ->visible(fn($get) => $get('type_sale') === 'auction'),
            Forms\Components\DateTimePicker::make('start_time')
                ->label('Thời gian bắt đầu')
                ->seconds(true)
                ->required()
                ->visible(fn($get) => $get('type_sale') === 'auction'),

            Forms\Components\DateTimePicker::make('end_time')
                ->label('Thời gian kết thúc')
                ->seconds(true)
                ->required()
                ->visible(fn($get) => $get('type_sale') === 'auction'),
            SelectTree::make('category_id')
                ->label('Danh mục')
                ->relationship('category', 'name', 'parent_id')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'active' => 'Hoạt động',
                    'inactive' => 'Dừng hoạt động',
                ])
                ->required(),
            Forms\Components\FileUpload::make('images')
                ->label('Hình ảnh')
                ->multiple()
                ->image()
                ->directory('product-images')
                ->preserveFilenames()
                ->reorderable()
                ->columnSpanFull()
                ->hidden(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
            TiptapEditor::make('description')
                ->label('Miêu tả sản phẩm')
                ->extraInputAttributes([
                    'style' => 'min-height: 400px;'
                ])
                ->required()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_hot')
                ->label('Sản phẩm ưu tiên')
                ->default(false),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\TextInput::make('seo.title')
                        ->label('SEO Title')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('seo.description')
                        ->label('SEO Description')
                        ->rows(3),
                    Forms\Components\TextInput::make('seo.keywords')
                        ->label('SEO Keywords')
                        ->placeholder('Từ khóa, cách nhau bởi dấu phẩy')
                        ->maxLength(255),
                ])
                ->columns(1)
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn(Builder $query) => $query->with('firstImage', 'category'),)
            ->columns([
                Tables\Columns\Textcolumn::make('name')
                    ->sortable()
                    ->label('Tên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Đường Dẫn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Miêu tả')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('view')
                    ->label('Lượt xem')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Số lượng')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_bid_amount')
                    ->label('Giá dưới')
                    ->money('VND')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_bid_amount')
                    ->label('Giá Trên')
                    ->money('VND')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                    })->formatStateUsing(fn(string $state): string => $state === 'active' ? 'hoạt động' : 'không hoạt động'),
                Tables\Columns\TextColumn::make('type_sale')
                    ->label('Dạng Sản Phẩm')
                    ->formatStateUsing(fn(string $state): string => $state === 'sale' ? 'Bán' : 'Đấu giá')
                    ->searchable(),
                Tables\Columns\TextColumn::make("start_time")
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->label('Thời Gian bắt đầu')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->label('Thời gian kết thúc')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('images.image_url')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->height(100)
                    ->width(100)
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->limit(50),
                Tables\Columns\TextColumn::make('is_hot')
                    ->label('Sản phẩm ưu tiên')
                    ->color(fn($state)  => match ($state) {
                        0 => 'success',
                        1 => 'danger'
                    })
                    ->formatStateUsing(fn($state) =>  $state ? 'có' : 'không'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->options(fn() => Category::pluck('name', 'id')->toArray())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('type_sale')
                    ->label('Dạng sản phẩm')
                    ->options([
                        'sale' => 'Bán trực tiếp',
                        'auction' => 'Đấu giá',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hoạt động',
                        'inactive' => 'Dừng hoạt động',
                    ]),

                Tables\Filters\SelectFilter::make('is_hot')
                    ->label('Sản phẩm ưu tiên')
                    ->options([
                        1 => 'Có',
                        0 => 'Không',
                    ]),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Giá từ')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Giá đến')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['min_price'], fn($q, $value) => $q->where('price', '>=', $value))
                            ->when($data['max_price'], fn($q, $value) => $q->where('price', '<=', $value));
                    }),

                Tables\Filters\Filter::make('auction_time')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Bắt đầu từ'),
                        Forms\Components\DatePicker::make('start_to')
                            ->label('Bắt đầu đến'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['start_from'], fn($q, $date) => $q->whereDate('start_time', '>=', $date))
                            ->when($data['start_to'], fn($q, $date) => $q->whereDate('start_time', '<=', $date));
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])

            ->actions([
                Tables\Actions\ViewAction::make('View')
                    ->label('Xem'),

                Tables\Actions\EditAction::make('Edit')
                    ->label('Sửa'),
                Tables\Actions\Action::make('delete')
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận xóa sản phẩm')
                    ->modalDescription('Bạn có chắc chắn muốn xóa sản phẩm này không? Thao tác này không thể hoàn tác.')
                    ->action(function (Product $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Thành côngg')
                            ->body('Sản phẩm xóa thành công!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa vĩnh viễn')
                        ->requiresConfirmation()
                        ->modalHeading('Xác nhận xóa vĩnh viễn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa vĩnh viễn các sản phẩm đã chọn không? Thao tác này không thể hoàn tác được?')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (empty($record->auction()->get()) || empty($record->orderDetails()->get())) {
                                    Notification::make()
                                        ->title('Lỗi')
                                        ->body("Không thể xóa sản phẩm '{$record->name}' vì nó có liên quan đến lệnh hoặc đấu giá.")
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }
                            $records->each->forceDelete();
                            Notification::make()
                                ->title('Thành Công')
                                ->body('Sản phẩm đã được xóa vĩnh viễn thành công!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Phục hồi')
                        ->action(function ($records) {
                            $records->each->restore();
                            Notification::make()
                                ->title('Thành Công')
                                ->body('Các sản phẩm đã được phục hồi thành công!')
                                ->success()
                                ->send();
                        })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductImageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
