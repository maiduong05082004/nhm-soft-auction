<?php

namespace App\Filament\Resources\PointPackageResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\PointPackageResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class BuyPointPackage extends Page
{
    protected static string $resource = PointPackageResource::class;

    protected static string $view = 'filament.admin.resources.points.buy-points-package';

    protected ?string $heading = 'Mua gói điểm';

    public static function canAccess( array $parameters = []): bool
    {
        return auth()->user()->hasRole(RoleConstant::CUSTOMER);
    }
    
}
