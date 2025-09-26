<?php

namespace App\Filament\Resources\CustomerInfoResource\Pages;

use App\Filament\Resources\CustomerInfoResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;


class ViewCustomerInfo extends Page
{
    protected static string $resource = CustomerInfoResource::class;

    protected static string $view = 'filament.admin.resources.users.user-info';

    protected ?string $heading = 'Thông tin cá nhân';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Chỉnh sửa')
                ->url(route('filament.admin.resources.customer-infos.edit'))
                ->icon('heroicon-o-pencil')
                ->color('primary'),
        ];
    }
}
