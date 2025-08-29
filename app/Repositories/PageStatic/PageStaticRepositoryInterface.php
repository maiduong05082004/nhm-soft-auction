<?php

namespace App\Repositories\PageStatic;

use App\Repositories\BaseRepositoryInterface;

interface PageStaticRepositoryInterface extends BaseRepositoryInterface
{
    public function getBySlug($slug);
}
