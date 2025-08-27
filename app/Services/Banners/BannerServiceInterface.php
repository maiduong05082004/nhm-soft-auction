<?php

namespace App\Services\Banners;

use App\Services\BaseServiceInterface;

interface BannerServiceInterface extends BaseServiceInterface
{
    public function getByNameTypeBanner(string $type);
}
