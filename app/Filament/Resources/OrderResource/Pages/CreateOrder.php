<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Support\Facades\Log;

class CreateOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = OrderResource::class;

    public function calculateTotal(): void
    {
        $items = $this->form->getState()['items'] ?? [];
        $total = 0;

        foreach ($items as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;
            }
        }

        $this->form->fill(['total' => $total]);
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                Wizard::make($this->getSteps())
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction($this->getSubmitFormAction())
                    ->skippable($this->hasSkippableSteps())
                    ->contained(false),
            ])
            ->columns(null);
    }

    protected function afterCreate(): void
    {
        /** @var Order $order */
        $order = $this->record;

        $order->loadMissing('items', 'items.product');
        $subtotal = 0.0;
        foreach ($order->items as $item) {
            $qty = (float) ($item->quantity ?? 0);
            $price = (float) ($item->product?->price ?? 0);
            $subtotal += $qty * $price;
        }
        $shippingFee = (float) ($order->shipping_fee ?? 0);
        $order->forceFill([
            'subtotal' => $subtotal,
            'total' => $subtotal + $shippingFee,
        ])->save();
    }

    /** @return Step[] */
    protected function getSteps(): array
    {
        return [
            Step::make('Order Details')
                ->schema([
                    Section::make()->schema(OrderResource::getDetailsFormSchema())->columns(),
                ]),

            Step::make('Order Items')
                ->schema([
                    Section::make()->schema([
                        OrderResource::getItemsRepeater(),
                        Placeholder::make('total_display')
                            ->label('Tổng tiền đơn hàng')
                            ->content(function (Get $get): string {
                                $items = $get('items') ?? [];
                                $total = 0;
                                foreach ($items as $item) {
                                    if (isset($item['quantity']) && isset($item['price'])) {
                                        $subtotal = $item['quantity'] * $item['price'];
                                        $total += $subtotal;
                                    }
                                }
                                return number_format($total, 0, ',', '.') . ' ₫';
                            })
                            ->columnSpan('full')
                            ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                    ]),
                ]),
                Step::make('Payment')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Section::make()->schema(OrderResource::getPaymentFormSchema()),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getState();
        $itemsState = is_array($state) ? ($state['items'] ?? []) : [];
        $subtotal = $this->computeSubtotalWithDebug($itemsState);
        $shippingFee = (float)($data['shipping_fee'] ?? 0);
        $data['subtotal'] = $subtotal;
        $data['total'] = $subtotal + $shippingFee;
        Log::debug('[OrderCreate] Totals', compact('subtotal', 'shippingFee') + ['total' => $data['total']]);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->form->getState();
        $itemsState = is_array($state) ? ($state['items'] ?? []) : [];
        $subtotal = $this->computeSubtotalWithDebug($itemsState);

        $shippingFee = (float)($data['shipping_fee'] ?? 0);
        $data['subtotal'] = $subtotal;
        $data['total'] = $subtotal + $shippingFee;
        Log::debug('[OrderSave] Totals', compact('subtotal', 'shippingFee') + ['total' => $data['total']]);

        return $data;
    }

    private function computeSubtotalWithDebug(array $items): float
    {
        $subtotal = 0.0;
        foreach ($items as $index => $item) {
            $quantity = (float)($item['quantity'] ?? 0);
            $productId = $item['product_id'] ?? null;
            $priceFromDb = $productId ? (float)(\App\Models\Product::find($productId)?->price ?? 0) : 0.0;
            $line = $quantity * $priceFromDb;
            $subtotal += $line;
            Log::debug('[OrderSubtotal] Line', [
                'idx' => $index,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price_from_db' => $priceFromDb,
                'line_subtotal' => $line,
            ]);
        }
        Log::debug('[OrderSubtotal] Subtotal computed', ['subtotal' => $subtotal]);
        return $subtotal;
    }
}
