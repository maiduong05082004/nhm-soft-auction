<?php

namespace App\Enums\Product;

enum ProductTypeSale: int
{
    case SALE = 1;
    case AUCTION = 2;

    public static function getOptions(): array
    {
        return [
            self::SALE->value => 'Bán hàng',
            self::AUCTION->value => 'Trả giá trực tuyến',
        ];
    }
}

