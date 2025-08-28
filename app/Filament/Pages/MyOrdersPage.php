<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\OrderDetail;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\OrderResource;

class MyOrdersPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string $view = 'filament.admin.pages.my-orders';
    protected static ?string $title = 'Đơn hàng của tôi';
    protected static ?string $navigationLabel = 'Đơn hàng của tôi';
    protected static ?string $navigationGroup = 'Đơn hàng';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if (is_callable([$user, 'hasRole'])) {
            return ! (bool) call_user_func([$user, 'hasRole'], 'admin');
        }
        return (string) ($user->role ?? '') !== 'admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderDetail::query()
                    ->with(['payments', 'user'])
                    ->where('user_id', auth()->id())
            )
            ->columns([
                Tables\Columns\TextColumn::make('code_orders')->label('Mã đơn')->searchable(),
                Tables\Columns\TextColumn::make('total')->label('Tổng')->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('status')->label('Trạng thái đơn')->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->state(function ($record) {
                        $payment = $record->payments->first();
                        return $payment?->status ?? 'pending';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'success' => 'Đã thanh toán',
                        'pending' => 'Chờ xác nhận',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('seller_confirmed')
                    ->label('Người bán xác nhận')
                    ->state(function ($record) {
                        $payment = $record->payments->first();
                        return !empty($payment?->confirmation_at) ? 'confirmed' : 'not_confirmed';
                    })
                    ->badge()
                    ->color(fn(string $state): string => $state === 'confirmed' ? 'success' : 'gray')
                    ->formatStateUsing(fn(string $state): string => $state === 'confirmed' ? 'Đã xác nhận' : 'Chưa xác nhận'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Ngày tạo'),
            ])
            ->actions([
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


