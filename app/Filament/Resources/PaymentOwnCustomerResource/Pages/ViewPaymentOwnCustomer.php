<?php

namespace App\Filament\Resources\PaymentOwnCustomerResource\Pages;

use App\Filament\Resources\PaymentOwnCustomerResource;
use Filament\Resources\Pages\Page;

class ViewPaymentOwnCustomer extends Page
{
    protected static string $resource = PaymentOwnCustomerResource::class;

    protected static string $view = 'filament.admin.resources.users.user-info-edit';


    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
