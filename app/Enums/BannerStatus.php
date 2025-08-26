<?php

namespace App\Enums;

enum BannerStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    public static function getOptions(): array
    {
        return [
            self::INACTIVE->value => 'Kích hoạt',
            self::ACTIVE->value => 'Ẩn',
        ];
    }

}
