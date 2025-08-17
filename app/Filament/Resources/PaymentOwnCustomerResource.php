<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentOwnCustomerResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentOwnCustomerResource extends Resource
{
    protected static ?string $model = User::class;

    public static ?string $navigationGroup = 'Thông tin';
    public static ?string $navigationLabel = 'Nạp tiền';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
