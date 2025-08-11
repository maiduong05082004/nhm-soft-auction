<?php

namespace App\Utils;

class HelperFunc
{
    public static function getTimestampAsId(): int
    {
        $microTime = microtime(true);
        $timestamp = (int)$microTime;
        $microSeconds = (int)(($microTime - $timestamp) * 1000000);
        // Format timestamp as ymdHisu (year, month, day, hour, minute, second, microsecond)
        $formatted = date('ymdHisu', $timestamp) . str_pad($microSeconds, 6, '0', STR_PAD_LEFT);
        return (int)$formatted;
    }


}
