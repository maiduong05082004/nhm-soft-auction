<?php

namespace App\Services\Banners;

use App\Enums\CommonConstant;
use App\Repositories\Banners\BannerRepository;
use App\Repositories\BannerTypes\BannerTypeRepository;
use App\Services\Banners\BannerServiceInterface;
use App\Services\BaseService;

class BannerService extends BaseService implements BannerServiceInterface
{
    public function __construct(BannerRepository $bannerRepository, BannerTypeRepository $bannerTypeRepository)
    {
        parent::__construct([
            'banner' => $bannerRepository,
            'bannerType' => $bannerTypeRepository
        ]);
    }

    public function getByNameTypeBanner(string $type)
    {
        $bannerType = $this->getRepository('bannerType')
            ->query()
            ->where([
                'name'   => $type,
                'status' => CommonConstant::ACTIVE
            ])
            ->first();
        if (!$bannerType) {
            return collect();
        }
        $banner = $this->getRepository('banner')
            ->query()
            ->where([
                'banner_type_id' => $bannerType->id,
                'status'         => CommonConstant::ACTIVE
            ])
            ->get();

        return $banner;
    }
}
