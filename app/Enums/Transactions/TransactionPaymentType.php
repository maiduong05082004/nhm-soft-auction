<?php

namespace App\Enums\Transactions;

enum TransactionPaymentType: int
{
    case BUY_PRODUCT = 1;
    case BID_PRODUCT = 2;
    case RECHANGE_POINT = 3;

    public static function label(int $type) : string {
        return match ($type) {
            self::BUY_PRODUCT->value => 'Mua Sản Phẩm',
            self::BID_PRODUCT->value => 'Đấu Giá ',
            self::RECHANGE_POINT->value => 'Mua Gói & Nạp'
        };
    }
}
