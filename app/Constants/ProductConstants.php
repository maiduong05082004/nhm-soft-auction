<?php

namespace App\Constants;

class ProductConstants
{
    public const STATES = [
        0 => 'chưa sử dụng',
        1 => 'hầu như không sử dụng',
        2 => 'không có vết xước hoặc bụi bẩn đáng chú ý',
        3 => 'có một số vết xước và bụi bẩn',
        4 => 'có vết xước và vết bẩn',
        5 => 'tình trạng chung là kém'
    ];

    public const PAY_METHODS = [
        0 => 'ship cod',
        1 => 'pay qr bank',
        2 => 'cả 2 phương thức'
    ];

    public static function label(string $type, int|string|null $value): string
    {
        return match ($type) {
            'states'  => self::STATES[$value] ?? 'Unknown',
            'pay_methods' => self::PAY_METHODS[$value] ?? 'Unknown',
            default  => 'Unknown',
        };
    }

    public static function options(string $type): array
    {
        return match ($type) {
            'states'  => self::STATES,
            'pay_methods' => self::PAY_METHODS,
            default  => [],
        };
    }
}
