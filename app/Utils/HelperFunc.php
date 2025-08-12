<?php

namespace App\Utils;

use Carbon\Carbon;

class HelperFunc
{
    public static function getTimestampAsId(): int
    {
       // Get microtime float
       $microFloat = microtime(true);
       $microTime = Carbon::createFromTimestamp($microFloat);
       $formatted = $microTime->format('ymdHisu');
       usleep(100);
       return (int)$formatted;
    }


}
