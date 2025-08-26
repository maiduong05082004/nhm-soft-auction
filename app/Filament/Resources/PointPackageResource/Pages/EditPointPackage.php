<?php

namespace App\Filament\Resources\PointPackageResource\Pages;

use App\Filament\Resources\PointPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointPackage extends EditRecord
{
    protected static string $resource = PointPackageResource::class;

    public static function canAccess(array $parameters = []): bool 
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
