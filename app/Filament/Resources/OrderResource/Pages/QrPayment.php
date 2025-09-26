<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\CreditCard;
use App\Models\OrderDetail;
use App\Models\Payment;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class QrPayment extends Page
{
    protected static string $resource = OrderResource::class;
    protected static string $view = 'filament.admin.resources.orders.qr-code';
    
    public OrderDetail $record;
    public Payment $payment;
    public ?CreditCard $creditCard = null;
    public array $sellerBreakdowns = [];

    public function mount(OrderDetail $record): void
    {
        $this->record = $record;
        $this->payment = Payment::where('order_detail_id', $record->id)->first();
        $items = Order::where('order_detail_id', $record->id)
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
            abort(404);
        }
    }

    public function getTitle(): string
    {
        return 'QR Code Thanh Toán - Đơn hàng ' . $this->record->code_orders;
    }

    public function getVietQRUrl(?CreditCard $card = null, ?float $amount = null, ?string $addInfo = null): string
    {
        $useCard = $card ?? $this->creditCard;
        $useAmount = $amount ?? (float) $this->payment->amount;
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

        $this->redirect(route('filament.admin.resources.orders.edit', ['record' => $this->record->id]));
    }
}