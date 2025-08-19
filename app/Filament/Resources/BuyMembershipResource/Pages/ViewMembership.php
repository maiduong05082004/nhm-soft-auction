<?php

namespace App\Filament\Resources\BuyMembershipResource\Pages;

use App\Filament\Resources\BuyMembershipResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ViewMembership extends Page
{
    protected static string $resource = BuyMembershipResource::class;

    protected static string $view = 'filament.admin.resources.membership.view-membership';

    protected ?string $heading = 'Gói thành viên';

    public function getBreadcrumbs(): array
    {
        return [
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make("buy_membership")
                ->icon('heroicon-o-user-group')
                ->label('Mua gói thành viên')
                ->url(fn (): string => BuyMembershipResource::getUrl('buy'))
        ];
    }
}
