<?php

namespace App\Repositories\Banners;

use App\Models\Banner;
use App\Repositories\BaseRepository;

class BannerRepository extends BaseRepository implements BannerRepositoryInterface
{
    public function getModel(): string
    {
        return Banner::class;
    }
}
