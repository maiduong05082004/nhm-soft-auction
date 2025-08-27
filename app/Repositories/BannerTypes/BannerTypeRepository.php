<?php

namespace App\Repositories\BannerTypes;

use App\Models\BannerType;
use App\Repositories\BaseRepository;

class BannerTypeRepository extends BaseRepository implements BannerTypeRepositoryInterface
{
    public function getModel(): string
    {
        return BannerType::class;
    }
}
