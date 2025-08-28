<?php

namespace App\Enums;

enum PaymentViewType: string
{
    case RECHARGE = '1';  
    case MEMBERSHIP = '2';
    case PRODUCT = '3';   

    public function label(): string
    {
        return match ($this) {
            self::RECHARGE   => 'Giao dịch nạp điểm',
            self::MEMBERSHIP => 'Giao dịch mua gói thành viên',
            self::PRODUCT    => 'Giao dịch sản phẩm',
        };
    }
}
