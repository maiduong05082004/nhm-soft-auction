<?php

namespace App\Enums\Product;

enum ProductState: int
{
    case UNUSED = 0;  // 'chưa sử dụng'
    case RARELY_USED = 1;  // 'hầu như không sử dụng'
    case EXCELLENT = 2;  // 'không có vết xước hoặc bụi bẩn đáng chú ý'
    case SOME_SCRATCHES = 3;  // 'có một số vết xước và bụi bẩn'
    case SCRATCHES_AND_DIRTY = 4;  // 'có vết xước và vết bẩn'
    case POOR_CONDITION = 5;  // 'tình trạng chung là kém'

    public static function getOptions(): array
    {
        return [
            self::UNUSED->value => 'Chưa sử dụng',
            self::RARELY_USED->value => 'Hầu như không sử dụng',
            self::EXCELLENT->value => 'Không có vết xước hoặc bụi bẩn đáng chú ý',
            self::SOME_SCRATCHES->value => 'Có một số vết xước và bụi bẩn',
            self::SCRATCHES_AND_DIRTY->value => 'Có vết xước và vết bẩn',
            self::POOR_CONDITION->value => 'Tình trạng chung là kém'
        ];
    }

    public static function getLabel(ProductState $state): string
    {
        return self::getOptions()[$state->value];
    }
}
