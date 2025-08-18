<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\CustomerInfoResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;

class CustomerInfoResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static ?string $navigationGroup = 'Thông tin';
    public static ?string $navigationLabel = 'Cá nhân';

    protected static ?string $modelLabel = 'Thông tin';
    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::CUSTOMER);
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewCustomerInfo::route('/'),
            'edit' => Pages\ViewCustomerInfoEdit::route('/edit'),
        ];
    }
}
