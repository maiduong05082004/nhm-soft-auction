<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\BuyMembershipResource\Pages;
use App\Models\MembershipPlan;
use Filament\Resources\Resource;

class BuyMembershipResource extends Resource
{
    protected static ?string $model = MembershipPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    public static ?string $navigationLabel = 'Gói thành viên';

    protected static ?string $modelLabel = "Gói thành viên";

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
            'index' => Pages\ViewMembership::route('/'),
            'buy' => Pages\BuyMemberships::route('/buy'),
        ];
    }
}
