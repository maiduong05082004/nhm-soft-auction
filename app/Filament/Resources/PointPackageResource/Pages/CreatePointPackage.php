<?php

namespace App\Filament\Resources\PointPackageResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\PointPackageResource;
use App\Services\PointPackages\PointPackageServiceInterface;
use Filament\Resources\Pages\CreateRecord;

class CreatePointPackage extends CreateRecord
{
    protected static string $resource = PointPackageResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort'] = app(PointPackageServiceInterface::class)->getAllPointPackage()->count() + 1;

        return $data;
    }
}
