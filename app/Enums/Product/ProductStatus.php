<?php

namespace App\Enums\Product;

enum ProductStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    public static function getOptions(): array
    {
        return [
            self::INACTIVE->value => 'Không mở bán',
            self::ACTIVE->value => 'Mở bán',
        ];
    }
}
