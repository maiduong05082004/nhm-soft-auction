<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
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
    public CreditCard $creditCard;

    public function mount(OrderDetail $record): void
    {
        $this->record = $record;
        $this->payment = Payment::where('order_detail_id', $record->id)->first();
        $this->creditCard = CreditCard::first();

        if (!$this->payment) {
            abort(404);
        }
    }

    public function getTitle(): string
    {
        return 'QR Code Thanh Toán - Đơn hàng ' . $this->record->code_orders;
    }

    public function getVietQRUrl(): string
    {
        $vietqrUrl = 'https://img.vietqr.io/image/'.$this->creditCard->bank.'-'.$this->creditCard->card_number.'-compact2.jpg';
        $vietqrUrl .= "?amount=" . $this->payment->amount;
        $vietqrUrl .= "&addInfo=" . urlencode("Thanh toan don hang " . $this->record->code_orders);
        $vietqrUrl .= "&accountName=" . urlencode($this->creditCard->name);
        
        return $vietqrUrl;
    }

    public function confirmPayment(): void
    {
        $this->payment->update([
            'status' => 'success',
            'pay_date' => now(),
        ]);

        $this->record->update([
            'status'=> 2,
        ]);
        Notification::make()
            ->title('Thanh toán đã được xác nhận!')
            ->success()
            ->send();

        $this->redirect(route('filament.admin.resources.orders.edit', ['record' => $this->record->id]));
    }
}