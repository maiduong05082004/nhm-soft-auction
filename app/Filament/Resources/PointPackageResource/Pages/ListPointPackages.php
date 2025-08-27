<?php

namespace App\Filament\Resources\PointPackageResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\PointPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointPackages extends ListRecords
{
    protected static string $resource = PointPackageResource::class;
    
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
