<?php

namespace App\Filament\Resources\CategoryArticleResource\Pages;

use App\Filament\Resources\CategoryArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryArticle extends EditRecord
{
    protected static string $resource = CategoryArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
