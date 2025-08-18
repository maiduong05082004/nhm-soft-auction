<?php

namespace App\Enums\Product;

enum ProductPaymentMethod: int
{
    case COD = 0;
    case QR_CODE = 1;
    case BOTH = 2;


    public static function getOptions(): array
    {
        return [
            self::COD->value => 'Ship COD',
            self::QR_CODE->value => 'Chuyển khoản ngân hàng',
            self::BOTH->value => 'Cả 2 phương thức'
        ];
    }
}
