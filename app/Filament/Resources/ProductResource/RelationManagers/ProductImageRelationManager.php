<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Utils\HelperFunc;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Layout\Grid;

class ProductImageRelationManager extends RelationManager
{

    protected static string $relationship = 'images'; // tên relation trong model Product

    protected static ?string $title = 'Hình ảnh sản phẩm';

    protected static ?string $modelLabel = "Hình ảnh sản phẩm";

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image_url')
                ->label('Hình ảnh')
                ->image()
                ->directory('product-images')
                ->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Grid::make(3)
                    ->schema([
                        Tables\Columns\ImageColumn::make('image_url')
                            ->label('Hình ảnh')
                            ->getStateUsing(function ($record) {
                                return HelperFunc::generateURLFilePath($record['image_url']);
                            })
                            ->height(80)
                            ->width(80),

                    ])
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Thêm hình ảnh sản phẩm')->modalHeading('Thêm hình ảnh sản phẩm'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
            ]);
    }
}
