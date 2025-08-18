<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\ConfigResource\Pages;
use App\Models\Config;
use Filament\Resources\Resource;

class ConfigResource extends Resource
{
    protected static ?string $model = Config::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    public static ?string $navigationGroup = 'Cấu hình hệ thống';
    public static ?string $navigationLabel = 'Config';

    protected static ?string $modelLabel = 'Config';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Config::route('/'),
        ];
    }
}
