<?php

namespace App\Repositories\PointPackage;

use App\Models\PointPackage;
use App\Repositories\BaseRepository;

class PointPackageRepository extends BaseRepository implements PointPackageRepositoryInterface
{

    public function getModel(): string
    {
        return PointPackage::class;
    }
}
