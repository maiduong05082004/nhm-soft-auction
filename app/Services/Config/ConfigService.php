<?php

namespace App\Services\Config;

use App\Repositories\Config\ConfigRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConfigService extends BaseService implements ConfigServiceInterface
{
    public function __construct(ConfigRepository $configRepo)
    {
        parent::__construct([
            'config' => $configRepo
        ]);
    }

    public function getAllConfig(): ?Collection
    {
        return $this->getAll('config');
    }

    public function updateConfigs(array $form):bool
    {
        try {
            DB::beginTransaction();
            foreach ($form as $key => $value) {
                $this->getRepository('config')->updateConfigWithKey($key, $value);
            }
            DB::commit();
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }
}
