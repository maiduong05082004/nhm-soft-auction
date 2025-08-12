<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use App\Services\Orders\OrderService;

class CreateOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = OrderResource::class;

    protected ?OrderService $orderService = null;

    protected function Service(): OrderService
    {
        return $this->orderService ??= app(OrderService::class);
    }

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
            Step::make('Chi tiết đơn hàng')
                ->schema([
                    Section::make()->schema(OrderResource::getDetailsFormSchema())->columns(),
                ]),

            Step::make('Sản phẩm trong đơn hàng')
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
            Step::make('Thanh toán')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Section::make()->schema(OrderResource::getPaymentFormSchema()),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->Service()->calculateOrderTotals($data);
    }

}
