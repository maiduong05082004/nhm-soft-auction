<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\OrderDetail;
use App\Services\Payments\PaymentServiceInterface;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\OrderResource;
use App\Enums\Permission\RoleConstant;

class CustomerOrdersPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static string $view = 'filament.admin.pages.customer-orders';
    protected static ?string $title = 'Đơn của khách hàng';
    protected static ?string $navigationLabel = 'Đơn của khách hàng';
    protected static ?string $navigationGroup = 'Đơn hàng';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(RoleConstant::CUSTOMER);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderDetail::query()
                    ->with(['payments', 'user'])
                    ->whereHas('items.product', function ($q) {
                        $q->where('created_by', auth()->id());
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('code_orders')->label('Mã đơn')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Khách hàng'),
                Tables\Columns\TextColumn::make('total')->label('Tổng')->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('status')->label('Trạng thái đơn')->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->state(function (OrderDetail $record) {
                        $payment = $record->payments->first();
                        if ($payment && (string) $payment->payment_method === '0') {
                            return 'direct';
                        }
                        return $payment?->status ?? 'pending';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'direct' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'success' => 'Đã thanh toán',
                        'pending' => 'Chờ xác nhận',
                        'direct' => 'Giao dịch trực tiếp',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('seller_confirmed')
                    ->label('Người bán xác nhận')
                    ->state(function (OrderDetail $record) {
                        $payment = $record->payments->first();
                        return !empty($payment?->confirmation_at) ? 'confirmed' : 'not_confirmed';
                    })
                    ->badge()
                    ->color(fn(string $state): string => $state === 'confirmed' ? 'success' : 'gray')
                    ->formatStateUsing(fn(string $state): string => $state === 'confirmed' ? 'Đã xác nhận' : 'Chưa xác nhận'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Ngày tạo'),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm_payment')
                    ->label('Xác nhận thanh toán')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (OrderDetail $record) {
                        $payment = $record->payments->first();
                        return $payment && empty($payment->confirmation_at);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận thanh toán')
                    ->modalDescription('Xác nhận rằng khách hàng đã chuyển tiền. Hệ thống sẽ lưu thời điểm xác nhận vào đơn này.')
                    ->action(function (OrderDetail $record) {
                        $paymentService = app(PaymentServiceInterface::class);
                        $ok = $paymentService->confirmPaymentBySeller($record->id, auth()->id());
                        if ($ok) {
                            Notification::make()->title('Thành công')->body('Đã xác nhận thanh toán cho đơn hàng.')->success()->send();
                        } else {
                            Notification::make()->title('Thất bại')->body('Bạn không có quyền hoặc đơn chưa có thanh toán.')->danger()->send();
                        }
                    })
                    ->after(function () {
                        if (method_exists($this, 'refreshTable')) {
                            $this->refreshTable();
                        }
                    }),
                Tables\Actions\ViewAction::make()
                    ->label('Xem')
                    ->modalHeading('Trạng thái thanh toán')
                    ->infolist(fn() => OrderResource::getInfolistSchema())
                    ->modalWidth(MaxWidth::SixExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng'),
            ])
            ->recordUrl(null);
    }
}
