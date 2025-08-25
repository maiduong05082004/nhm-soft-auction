<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            url()->previous() => 'Đơn hàng',
            '' => 'Danh sách',
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo đơn hàng mới'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return OrderResource::getWidgets();
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        if ($user->hasRole(RoleConstant::CUSTOMER)) {
            return [];
        }
        return [
            null => Tab::make('Tất cả'),
            'Đơn mới' => Tab::make()->query(fn($query) => $query->where('status', '1')),
            'Đang xử lý' => Tab::make()->query(fn($query) => $query->where('status', '2')),
            'Đã giao' => Tab::make()->query(fn($query) => $query->where('status', '3')),
            'Đã giao' => Tab::make()->query(fn($query) => $query->where('status', '4')),
            'Đã hủy' => Tab::make()->query(fn($query) => $query->where('status', '5')),
        ];
    }
}
