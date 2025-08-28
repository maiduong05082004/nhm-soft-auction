<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED  = 'failed';

    public static function getOptions(): array
    {
        return [
            self::PENDING->value => 'Đang xử lý',
            self::SUCCESS->value => 'Thành công',
            self::FAILED->value  => 'Thất bại',
        ];
    }

    public static function getLabel(PaymentStatus $status): string
    {
        return self::getOptions()[$status->value];
    }

    public static function getColor(PaymentStatus $status): string
    {
        return match ($status) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED  => 'danger',
            default       => 'default',
        };
    }
}
