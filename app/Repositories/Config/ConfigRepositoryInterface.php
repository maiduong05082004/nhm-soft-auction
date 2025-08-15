<?php

namespace App\Repositories\Config;

use App\Repositories\BaseRepositoryInterface;

interface ConfigRepositoryInterface extends BaseRepositoryInterface
{
    public function updateConfigWithKey($key, $value): int;

}
