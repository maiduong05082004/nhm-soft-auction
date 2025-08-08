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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                ->live(onBlur: true),
            Forms\Components\TextInput::make('slug')
                ->label('Đường dẫn')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->suffixAction(
                    \Filament\Forms\Components\Actions\Action::make('generateSlug')
                        ->label('Tự động tạo')
                        ->icon('heroicon-m-arrow-path')
                        ->action(function ($get, $set) {
                            $title = $get('name');
                            if ($title) {
                                $set('slug', \Illuminate\Support\Str::slug($title));
                            }
                        })
                ),

            Forms\Components\TextInput::make('price')
                ->label('Giá')
                ->numeric()
                ->required(),

            Forms\Components\Textarea::make('description')
                ->label('Miêu tả')
                ->autosize(),

            Forms\Components\TextInput::make('stock')
                ->label('Số lượng')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('min_bid_amount')
                ->label('Giá Dưới')
                ->numeric()
                ->required(),


            Forms\Components\TextInput::make('max_bid_amount')
                ->label('Giá Trên')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('type_sale')
                ->label('Dạng sản phảm')
                ->options([
                    'sale' => 'Sale',
                    'auction' => 'Auction',
                ])
                ->required()
                ->live(),


            SelectTree::make('category_id')
                ->label('Danh mục')
                ->relationship('category', 'name', 'parent_id')
                ->searchable()
                ->required(),

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

            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'inactive',
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
                ->hidden(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)

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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Miêu tả')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('max_bid_amount')
                    ->label('Giá Trên')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_sale')
                    ->label('Dạng Sản Phẩm')
                    ->searchable(),
                Tables\Columns\TextColumn::make("start_time")
                    ->dateTime()
                    ->label('Thời Gian bắt đầu')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->label('Thời gian kết thúc')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('firstImage.image_url')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->height(100)
                    ->width(100)
                    ->defaultImageUrl(fn($record) => gettype($record->image_url) === 'string' ? asset('storage/' . $record->image_url) : null),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->limit(50)

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Danh mục'),
                Tables\Filters\SelectFilter::make('start_time')
                    ->label('Thời gian bắt đầu'),
                    Tables\Filters\SelectFilter::make('end_time')
                    ->label('Thời gian kết thúc'),
                Tables\Filters\TrashedFilter::make()

            ])
            ->actions([
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
