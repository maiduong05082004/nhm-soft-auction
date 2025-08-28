<?php

namespace App\Filament\Resources\PageSaticResoucreResource\Pages;

use App\Filament\Resources\PageSaticResoucreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageSaticResoucres extends ListRecords
{
    protected static string $resource = PageSaticResoucreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
