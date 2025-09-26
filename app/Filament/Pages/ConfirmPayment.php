<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\CreditCard;
use App\Models\OrderDetail;
use App\Models\Payment;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ConfirmPayment extends Page
{
    protected static string $view = 'filament.admin.pages.confirm-payment';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    
    public OrderDetail $record;
    public ?Payment $payment = null;
    public ?CreditCard $creditCard = null;
    public array $sellerBreakdowns = [];

    public function mount($record = null): void
    {
        $recordId = is_object($record) ? ($record->id ?? null) : ($record ?? null);
        if (!$recordId) {
            $recordId = (string) request('record');
        }

        $this->record = OrderDetail::findOrFail($recordId);
        $this->payment = Payment::where('order_detail_id', $this->record->id)->first();

        if ((int) ($this->record->user_id) !== (int) (Auth::id())) {
            abort(403);
        }

        $firstItem = Order::where('order_detail_id', $this->record->id)
            ->with('product.owner.creditCard')
            ->first();

        $this->creditCard = $firstItem?->product?->owner?->creditCard;

        $items = \App\Models\Order::where('order_detail_id', $this->record->id)
            ->with(['product.owner.creditCard'])
            ->get();

        $grouped = [];
        foreach ($items as $item) {
            $seller = $item->product?->owner;
            if (!$seller) {
                continue;
            }
            $sellerId = (int) $seller->id;
            if (!isset($grouped[$sellerId])) {
                $grouped[$sellerId] = [
                    'seller_id' => $sellerId,
                    'seller_name' => (string) ($seller->name ?? ('Seller #' . $sellerId)),
                    'amount' => 0.0,
                    'credit_card' => $seller->creditCard,
                ];
            }
            $lineTotal = (float) ($item->total ?? 0);
            if ($lineTotal === 0.0) {
                $unit = (float) ($item->product?->price ?? 0);
                $qty = (float) ($item->quantity ?? 0);
                $lineTotal = $unit * $qty;
            }
            $grouped[$sellerId]['amount'] += $lineTotal;
        }

        foreach ($grouped as $sellerId => $data) {
            $this->sellerBreakdowns[] = [
                'seller_id' => $sellerId,
                'seller_name' => $data['seller_name'],
                'amount' => $data['amount'],
                'credit_card' => $data['credit_card'],
                'add_info' => 'Thanh toan don hang ' . $this->record->code_orders . ' - Seller ' . $data['seller_name'],
            ];
        }

        if (!$this->payment) {
            Notification::make()
                ->title('Chưa có thông tin thanh toán')
                ->body('Đơn hàng này chưa có bản ghi thanh toán. Vui lòng thử lại sau.')
                ->warning()
                ->send();
            $this->redirect(\App\Filament\Pages\MyOrdersPage::getUrl());
            return;
        }
        if (!$this->creditCard) {
            Notification::make()
                ->title('Thiếu thông tin tài khoản người bán')
                ->body('Người bán chưa cấu hình tài khoản ngân hàng. Vui lòng liên hệ người bán để cập nhật.')
                ->warning()
                ->send();
            $this->redirect(\App\Filament\Pages\MyOrdersPage::getUrl());
            return;
        }
    }

    public function getTitle(): string
    {
        return 'Thanh Toán - Đơn hàng ' . $this->record->code_orders;
    }

    public function getVietQRUrl(?CreditCard $card = null, ?float $amount = null, ?string $addInfo = null): string
    {
        $useCard = $card ?? $this->creditCard;
        $useAmount = $amount ?? (float) ($this->payment->amount ?? 0);
        $useInfo = $addInfo ?? ('Thanh toan don hang ' . $this->record->code_orders);
        if (!$useCard) {
            return '';
        }
        $vietqrUrl = 'https://img.vietqr.io/image/'.$useCard->bin_bank.'-'.$useCard->card_number.'-compact2.jpg';
        $vietqrUrl .= "?amount=" . $useAmount;
        $vietqrUrl .= "&addInfo=" . urlencode($useInfo);
        $vietqrUrl .= "&accountName=" . urlencode($useCard->name);
        return $vietqrUrl;
    }

    public function confirmPayment(): void
    {
        if (!$this->payment) {
            return;
        }
        $this->payment->update([
            'status' => PaymentStatus::SUCCESS->value,
            'pay_date' => now(),
        ]);

        $this->record->update([
            'status'=> OrderStatus::Processing->value,
        ]);
        Notification::make()
            ->title('Thanh toán đã được xác nhận!')
            ->success()
            ->send();
            
        $this->redirect(\App\Filament\Pages\MyOrdersPage::getUrl());
    }
}