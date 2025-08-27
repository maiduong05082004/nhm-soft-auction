<?php

namespace App\Enums\Transactions;

enum TransactionPaymentStatus: int
{
    case WAITING = 1;
    case ACTIVE = 2;
    case FAILED = 3;

    public static function getLabel(int $value): string
    {
        return match ($value) {
            self::WAITING->value => 'Đang chờ xử lý',
            self::ACTIVE->value => 'Thành công',
            self::FAILED->value => 'Thất bại',
            default => 'Không xác định',
        };
    }
}
