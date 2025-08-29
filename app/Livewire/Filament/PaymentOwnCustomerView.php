<?php

namespace App\Livewire\Filament;

use App\Enums\Config\ConfigName;
use App\Enums\PaymentStatus;
use App\Enums\Product\ProductPaymentMethod;
use App\Enums\Transactions\TransactionPaymentStatus;
use App\Enums\Transactions\TransactionPaymentType;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\TransactionPayment;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Config\ConfigServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use App\Services\OrderDetails\OrderDetailServiceInterface;
use App\Services\PointPackages\PointPackageServiceInterface;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PaymentOwnCustomerView extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public ?Authenticatable $auth;
    public $sumTransaction;
    public ?array $data = [];
    public string $viewType = '1';
    public $current_balance = 0;
    public $config;
    private AuthServiceInterface $authService;
    private PointPackageServiceInterface $pointPackageService;
    private MembershipServiceInterface $membershipService;
    private OrderDetailServiceInterface $orderDetailService;
    private ConfigServiceInterface $configService;

    public function boot(
        AuthServiceInterface $authService,
        PointPackageServiceInterface $pointPackageService,
        MembershipServiceInterface $membershipService,
        OrderDetailServiceInterface $orderDetailService,
        ConfigServiceInterface $configService
    ): void {
        $this->authService          = $authService;
        $this->pointPackageService  = $pointPackageService;
        $this->membershipService    = $membershipService;
        $this->orderDetailService   = $orderDetailService;
        $this->configService        = $configService;
    }

    public function mount(): void
    {
        $this->config = $this->configService->getConfigByKeys([
            ConfigName::PRICE_ONE_COIN,
        ]);
        $this->auth = $this->authService->getInfoAuth();
        $this->sumTransaction = $this->authService->getSumTransaction();
        $this->current_balance =  (int) $this->auth->current_balance * (int) $this->config['PRICE_ONE_COIN'] ;
    }

    protected function getTableQuery(): Builder
    {
        $userId = $this->auth?->id ?? auth()->id();

        return match ($this->viewType) {
            '1'     => TransactionPayment::where('user_id', $userId)->where('description', '!=', 'PAY BY POINTS')->latest(),
            '2'     => MembershipTransaction::where('user_id', $userId)->latest(),
            // '3'     => Payment::where('user_id', $userId)->latest(),
            default => TransactionPayment::where('user_id', $userId)->latest(),
        };
    }

    protected function moneyColumn(string $field): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make($field)
            ->label('Số tiền')
            ->sortable()
            ->formatStateUsing(fn($state) => number_format($state ?? 0, 0, ',', '.'));
    }

    protected function createdAtColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('created_at')
            ->label('Thời điểm giao dịch')
            ->dateTime()
            ->sortable();
    }

    protected function getTableColumns(): array
    {
        return match ($this->viewType) {
            '1' => [
                Tables\Columns\TextColumn::make('description')->label('Mã GD')->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->toggleable()
                    ->formatStateUsing(fn($state) => TransactionPaymentType::label($state ?? '')),
                $this->moneyColumn('money'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(function ($state) {
                        $int = is_numeric($state) ? (int)$state : null;
                        return $int !== null ? TransactionPaymentStatus::getLabel($int) : $state;
                    })
                    ->color(function ($state) {
                        if (!is_numeric($state)) return 'default';
                        return match (TransactionPaymentStatus::from((int)$state)) {
                            TransactionPaymentStatus::WAITING => 'warning',
                            TransactionPaymentStatus::ACTIVE  => 'success',
                            TransactionPaymentStatus::FAILED  => 'danger',
                            default => 'default',
                        };
                    }),
                $this->createdAtColumn(),
            ],

            '2' => [
                Tables\Columns\TextColumn::make('transaction_code')->label('Mã GD')->sortable(),
                $this->moneyColumn('money'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state) => is_numeric($state) ? TransactionPaymentStatus::getLabel((int)$state) : $state)
                    ->color(fn($state) => is_numeric($state) ? match (TransactionPaymentStatus::from((int)$state)) {
                        TransactionPaymentStatus::WAITING => 'warning',
                        TransactionPaymentStatus::ACTIVE  => 'success',
                        TransactionPaymentStatus::FAILED  => 'danger',
                        default => 'default',
                    } : 'default'),
                $this->createdAtColumn(),
            ],

            '3' => [
                Tables\Columns\TextColumn::make('transaction_id')->label('Mã GD')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->toggleable()
                    ->formatStateUsing(fn($state) => ProductPaymentMethod::getLabel(ProductPaymentMethod::from((int)$state))),
                $this->moneyColumn('amount'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state) => PaymentStatus::getLabel(PaymentStatus::from($state)))
                    ->color(fn($state) => PaymentStatus::getColor(PaymentStatus::from($state))),
                $this->createdAtColumn(),
            ],

            default => [],
        };
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->label('Trạng thái')
                ->options([
                    TransactionPaymentStatus::WAITING->value => 'Đang xử lý',
                    TransactionPaymentStatus::ACTIVE->value  => 'Thành công',
                    TransactionPaymentStatus::FAILED->value  => 'Thất bại',
                ]),
            Tables\Filters\Filter::make('date_range')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('Từ'),
                    Forms\Components\DatePicker::make('to')->label('Đến'),
                ])
                ->query(
                    fn(Builder $query, array $data) =>
                    $query
                        ->when($data['from'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                        ->when($data['to'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
                ),
        ];
    }

    public function setViewType(string $type): void
    {
        $this->viewType = $type;
        method_exists($this, 'resetTable')
            ? $this->resetTable()
            : $this->emitSelf('refresh');
    }

    public function render()
    {
        return view('livewire.filament.payment-own-customer-view');
    }
}
