<?php

namespace App\Filament\Resources\PageStaticResource\Pages;

use App\Filament\Resources\PageStaticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageStatics extends ListRecords
{
    protected static string $resource = PageStaticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
