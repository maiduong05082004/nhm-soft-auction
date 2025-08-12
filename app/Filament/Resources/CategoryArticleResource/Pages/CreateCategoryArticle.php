<?php

namespace App\Filament\Resources\CategoryArticleResource\Pages;

use App\Filament\Resources\CategoryArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryArticle extends CreateRecord
{
    protected static string $resource = CategoryArticleResource::class;
}
