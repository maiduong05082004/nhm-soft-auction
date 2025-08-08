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

    protected static ?string $modelLabel = 'Products';

    protected static ?string  $pluralModelLabel = 'Products';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->suffixAction(
                    \Filament\Forms\Components\Actions\Action::make('generateSlug')
                        ->label('Auto generate')
                        ->icon('heroicon-m-arrow-path')
                        ->action(function ($get, $set) {
                            $title = $get('name');
                            if ($title) {
                                $set('slug', \Illuminate\Support\Str::slug($title));
                            }
                        })
                ),

            Forms\Components\TextInput::make('price')
                ->label('Price')
                ->numeric()
                ->required(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->autosize(),

            Forms\Components\TextInput::make('stock')
                ->label('Stock')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('min_bid_amount')
                ->label('Min Bid Amount')
                ->numeric()
                ->required(),


            Forms\Components\TextInput::make('max_bid_amount')
                ->label('Max Bid Amount')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('type_sale')
                ->label('Type')
                ->options([
                    'sale' => 'Sale',
                    'auction' => 'Auction',
                ])
                ->required()
                ->live(),


            SelectTree::make('category_id')
                ->label('Category')
                ->relationship('category', 'name', 'parent_id')
                ->searchable()
                ->required(),

            Forms\Components\DateTimePicker::make('start_time')
                ->label('Start Time')
                ->seconds(true)
                ->required()
                ->visible(fn($get) => $get('type_sale') === 'auction'),

            Forms\Components\DateTimePicker::make('end_time')
                ->label('End Time')
                ->seconds(true)
                ->required()
                ->visible(fn($get) => $get('type_sale') === 'auction'),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'inactive',
                ])
                ->required(),
            Forms\Components\FileUpload::make('images')
                ->label('Product Images')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('view')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_bid_amount')
                    ->label('min amount')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('status')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('max_bid_amount')
                    ->label('max amount')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_sale')
                    ->label('type sale')
                    ->searchable(),
                Tables\Columns\TextColumn::make("start_time")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('firstImage.image_url')
                    ->label('Image')
                    ->disk('public')
                    ->height(100)
                    ->width(100)
                    ->defaultImageUrl(fn($record) => gettype($record->image_url) === 'string' ? asset('storage/' . $record->image_url) : null),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('category')
                    ->limit(50)

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category'),
                Tables\Filters\TrashedFilter::make()

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Course Deletion')
                    ->modalDescription('Are you sure you want to delete this course? This action cannot be undone.')
                    ->action(function (Product $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Success')
                            ->body('Course deleted successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete permanetly')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm delete permanently')
                        ->modalDescription('Are you sure you want to delete permantly the selected products? This action cannot be undo?')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                // dd($record->auction()->get(),$record->orderDetails()->get() );
                                if (empty($record->auction()->get()) || empty($record->orderDetails()->get())) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body("Cannot delete product '{$record->name}' because it has related order or auction.")
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }
                            $records->each->forceDelete();
                            Notification::make()
                                ->title('Success')
                                ->body('The product have been deleted permantly successfully!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore')
                        ->action(function ($records) {
                            $records->each->restore();
                            Notification::make()
                                ->title('Success')
                                ->body('The products have been restored successfully!')
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
