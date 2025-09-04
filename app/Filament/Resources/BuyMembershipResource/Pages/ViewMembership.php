<?php

namespace App\Filament\Resources\BuyMembershipResource\Pages;

use App\Enums\CommonConstant;
use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\BuyMembershipResource;
use App\Services\Auth\AuthServiceInterface;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ViewMembership extends Page
{
    protected static string $resource = BuyMembershipResource::class;

    protected static string $view = 'filament.admin.resources.membership.view-membership';

    protected ?string $heading = 'Gói thành viên';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $user = app(AuthServiceInterface::class)->getInfoAuth();
        $membershipUsers = collect($user['membershipUsers'] ?? []);
        $planActive = $membershipUsers->first(function ($item) {
            $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
            return $status == CommonConstant::ACTIVE;
        });
        if (empty($planActive)) {
            return [
                Actions\Action::make("buy_membership")
                    ->icon('heroicon-o-user-group')
                    ->label('Mua gói thành viên')
                    ->url(fn(): string => BuyMembershipResource::getUrl('buy'))
            ];
        }else {
            return [];
        }
    }
}
