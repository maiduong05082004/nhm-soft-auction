<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\Widget;

class OrderSidebar extends Widget
{
    protected static string $view = 'filament.admin.resources.orders.sidebar-links';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if (is_callable([$user, 'hasRole'])) {
            return ! (bool) call_user_func([$user, 'hasRole'], 'admin');
        }
        return (string) ($user->role ?? '') !== 'admin';
    }
}


