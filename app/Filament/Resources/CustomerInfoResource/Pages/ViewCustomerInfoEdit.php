<?php

namespace App\Filament\Resources\CustomerInfoResource\Pages;

use App\Filament\Resources\CustomerInfoResource;
use Filament\Resources\Pages\Page;
class ViewCustomerInfoEdit extends Page
{
    protected static string $resource = CustomerInfoResource::class;

    protected static string $view = 'filament.admin.resources.users.user-info-edit';

    protected ?string $heading = 'Chỉnh sửa thông tin cá nhân';

    public function getBreadcrumbs(): array
    {
        return [
            url()->route('filament.admin.resources.customer-infos.index') => 'Thông tin cá nhân',
        ];
    }
}
