<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Enums\Product\ProductPaymentMethod;
use App\Enums\Product\ProductState;
use App\Enums\Product\ProductStatus;
use App\Enums\Product\ProductTypeSale;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
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
use FilamentTiptapEditor\Enums\TiptapOutput;
use App\Utils\HelperFunc;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string  $pluralModelLabel = 'Sản phẩm';
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Tên')
                ->required()
                ->maxLength(255)
                ->live(debounce: 500)
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!$state) {
                        $set('slug', '');
                        return;
                    };
                    $baseSlug = Str::slug($state);
                    $slug = $baseSlug . '-' . HelperFunc::getTimestampAsId();
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
                ->options(ProductTypeSale::getOptions())
                ->required()
                ->default(ProductTypeSale::SALE->value)
                ->live(),
            Forms\Components\TextInput::make('price')
                ->label('Giá')
                ->numeric()
                ->requiredIf('type_sale', ProductTypeSale::SALE->value)
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::SALE->value;
                })
                ->dehydrated(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::SALE->value;
                }),
            Forms\Components\TextInput::make('stock')
                ->label('Số lượng')
                ->numeric()
                ->minValue(1)
                ->required(),
            Forms\Components\TextInput::make('min_bid_amount')
                ->label('Giá Dưới')
                ->numeric()
                ->default(0)
                ->requiredIf('type_sale', ProductTypeSale::AUCTION->value)
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::AUCTION->value;
                }),
            Forms\Components\TextInput::make('max_bid_amount')
                ->label('Giá Trên')
                ->numeric()
                ->default(0)
                ->requiredIf('type_sale', ProductTypeSale::AUCTION->value)
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::AUCTION->value;
                })
                ->rules([
                    fn($get) => function ($attribute, $value, $fail) use ($get) {
                        $t = $get('type_sale');
                        $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                        if ($v === ProductTypeSale::AUCTION->value) {
                            $min = $get('min_bid_amount');
                            if ($min !== null && $value <= $min) {
                                $fail('Giá trên phải lớn hơn giá dưới.');
                            }
                        }
                    }
                ]),
            Forms\Components\TextInput::make('step_price')
                ->label('Bước giá')
                ->numeric()
                ->default(10000)
                ->requiredIf('type_sale', ProductTypeSale::AUCTION->value)
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::AUCTION->value;
                })
                ->dehydrated(false)
                ->default(function (?\App\Models\Product $record) {
                    return $record?->auction?->step_price;
                }),
            Forms\Components\DateTimePicker::make('start_time')
                ->label('Thời gian bắt đầu')
                ->seconds(true)
                ->required()
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::AUCTION->value;
                }),
            Forms\Components\DateTimePicker::make('end_time')
                ->label('Thời gian kết thúc')
                ->seconds(true)
                ->required()
                ->visible(function ($get) {
                    $t = $get('type_sale');
                    $v = $t instanceof \App\Enums\Product\ProductTypeSale ? $t->value : (int) $t;
                    return $v === ProductTypeSale::AUCTION->value;
                }),
            SelectTree::make('category_id')
                ->label('Danh mục')
                ->formatStateUsing(fn($state) => (string) $state)
                ->relationship('category', 'name', 'parent_id')
                ->searchable()
                ->expandSelected(true)
                ->enableBranchNode()
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options(ProductStatus::getOptions())
                ->required(),
            Forms\Components\Select::make('state')
                ->label('Tình trạng sản phẩm')
                ->required()
                ->options(ProductState::getOptions())
                ->default(ProductState::UNUSED),
            Forms\Components\Select::make('pay_method')
                ->label('Phương thức thanh toán')
                ->required()
                ->options(ProductPaymentMethod::getOptions())
                ->default(ProductPaymentMethod::BOTH),
            Forms\Components\TextInput::make('brand')
                ->label('Thương hiệu'),
            Forms\Components\FileUpload::make('images')
                ->required()
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
                ->output(TiptapOutput::Html)
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
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->limit(50)
                    ->label('Tên')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable()
                    ->searchable(),
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
                    ->color(fn($state) => match ($state) {
                        ProductStatus::ACTIVE->value => 'success',
                        ProductStatus::INACTIVE->value => 'warning',
                        default => 'default',
                    })->formatStateUsing(fn($state) => $state ? 'hoạt động' : 'không hoạt động'),
                Tables\Columns\TextColumn::make('type_sale')
                    ->label('Dạng Sản Phẩm')
                    ->formatStateUsing(fn($state): string => $state == 1 ? 'Bán trực tiếp' : ($state == 2 ? 'Đấu giá' : 'Không xác định'))
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
                    ->getStateUsing(fn($record) => HelperFunc::generateURLFilePath($record->images->pluck('image_url')->first()))
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
                Tables\Columns\TextColumn::make('state')
                    ->label('Tình trạng sản phẩm')
                    ->formatStateUsing(fn($state) => ProductState::getLabel(ProductState::from($state))),
                Tables\Columns\TextColumn::make('pay_method')
                    ->label('Phương thức thanh toán')
                    ->formatStateUsing(fn($state) => ProductPaymentMethod::getLabel(ProductPaymentMethod::from($state))),
                Tables\Columns\TextColumn::make('brand')
                    ->label("Thương hiệu"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->options(fn() => Category::pluck('name', 'id')->toArray())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('type_sale')
                    ->label('Dạng sản phẩm')
                    ->options([
                        1 => 'Bán trực tiếp',
                        2 => 'Đấu giá',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Hoạt động',
                        0 => 'Dừng hoạt động',
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
            ])
            ->emptyStateHeading("Chưa có sản phẩm nào")
            ->emptyStateDescription('Một khi bạn đăng bán 1 sản phẩm, nó sẽ xuất hiện ở đây.')
            ->emptyStateIcon("heroicon-o-rectangle-stack")
            ->emptyStateActions([
                TableAction::make('create')
                    ->label('Đăng sản phẩm')
                    ->url(route('filament.admin.resources.products.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
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
            'view' => Pages\ViewProducts::route('/{record}'),
        ];
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return 'slug';
    }
}
