<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use App\Services\Orders\OrderService;
use App\Utils\HelperFunc;
use Filament\Notifications\Notification;

class CreateOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = OrderResource::class;

    protected ?OrderService $orderService = null;

    protected function Service(): OrderService
    {
        return $this->orderService ??= app(OrderService::class);
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
        $formData = $this->form->getState();
        $paymentMethod = $formData['payment_method'] ?? '0';

        $this->Service()->afterCreate($order, $paymentMethod);

        if ($paymentMethod === '1') {
            Notification::make()
                ->title('Đã chuyển đến trang QR code thanh toán!')
                ->info()
                ->send();
        } else {
            Notification::make()
                ->title('Đơn hàng đã được tạo thành công!')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        $formData = $this->form->getState();
        $paymentMethod = $formData['payment_method'] ?? '0';

        if ($paymentMethod === '1') {
            return route('filament.admin.resources.orders.qr-code', ['record' => $this->record]);
        }

        return parent::getRedirectUrl();
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
                                return $this->Service()->formatCurrency($this->Service()->calculateSubtotal($items));
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
