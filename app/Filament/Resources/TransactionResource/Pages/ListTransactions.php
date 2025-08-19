<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ListTransactions extends Page
{
    protected static string $resource = TransactionResource::class;

    protected static string $view = 'filament.admin.resources.transaction.list-transaction';

    protected ?string $heading = 'Thống kê thanh toán';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
