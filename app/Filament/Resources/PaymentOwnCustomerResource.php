<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\PaymentOwnCustomerResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;

class PaymentOwnCustomerResource extends Resource
{
    protected static ?string $model = User::class;

    public static ?string $navigationGroup = 'Thông tin';
    public static ?string $navigationLabel = 'Thống kê thanh toán';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Thông tin';
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::CUSTOMER);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewPaymentOwnCustomer::route('/'),
        ];
    }
}
