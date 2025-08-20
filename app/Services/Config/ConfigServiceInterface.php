<?php

namespace App\Services\Config;

use App\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface ConfigServiceInterface extends BaseServiceInterface
{
    public function getAllConfig() : ?Collection;

    public function updateConfigs(array $form): bool;

    public function getConfigByKeys(array $keys);

    public function getConfigValue(string $configKey, $default = null);
}
