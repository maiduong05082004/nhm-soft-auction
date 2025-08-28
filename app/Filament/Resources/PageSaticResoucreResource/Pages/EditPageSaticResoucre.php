<?php

namespace App\Filament\Resources\PageSaticResoucreResource\Pages;

use App\Filament\Resources\PageSaticResoucreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageSaticResoucre extends EditRecord
{
    protected static string $resource = PageSaticResoucreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
