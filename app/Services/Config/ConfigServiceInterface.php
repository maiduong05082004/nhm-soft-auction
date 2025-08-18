<?php

namespace App\Services\Config;

use App\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface ConfigServiceInterface extends BaseServiceInterface
{
    public function getAllConfig() : ?Collection;

    public function updateConfigs(array $form): bool;

}
