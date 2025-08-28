<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\OrderResource;
use App\Models\OrderDetail;
use App\Services\Orders\OrderService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $navigationLabel = 'Chi tiết đơn hàng';

    protected ?OrderService $orderService = null;

    protected function Service(): OrderService
    {
        return $this->orderService ??= app(OrderService::class);
    }

    protected function getActions(): array
    {
        if(auth()->user()->hasRole(RoleConstant::ADMIN)) {
        return [
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
                Actions\ForceDeleteAction::make(),
            ];
        }
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Chỉnh sửa đơn hàng';
    }

    protected function afterSave(): void
    {
        $orderDetail = $this->record;
        $this->Service()->syncOrderTotals($orderDetail);
    }
}
