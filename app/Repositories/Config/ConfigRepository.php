<?php

namespace App\Repositories\Config;

use App\Models\Config;
use App\Repositories\BaseRepository;

class ConfigRepository extends BaseRepository implements ConfigRepositoryInterface
{
    public function getModel(): string
    {
        return Config::class;
    }

    public function updateConfigWithKey($key, $value): int
    {
        return $this->model->newQuery()->where('config_key', $key)->update(['config_value' => $value]);
    }
}
