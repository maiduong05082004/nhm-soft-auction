<?php

namespace App\Filament\Resources\CategoryArticleResource\Pages;

use App\Filament\Resources\CategoryArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryArticles extends ListRecords
{
    protected static string $resource = CategoryArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
