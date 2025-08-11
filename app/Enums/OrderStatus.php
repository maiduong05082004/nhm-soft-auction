<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: int implements HasColor, HasIcon, HasLabel
{
    case New = 1;

    case Processing = 2;

    case Shipped = 3;

    case Delivered = 4;

    case Cancelled = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Đơn mới',
            self::Processing => 'Đang xử lý',
            self::Shipped => 'Đã giao',
            self::Delivered => 'Đã giao',
            self::Cancelled => 'Đã hủy',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New => 'info',
            self::Processing => 'warning',
            self::Shipped, self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Shipped => 'heroicon-m-truck',
            self::Delivered => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}
