<?php

namespace App\Services\PageStatic;

use App\Services\BaseServiceInterface;

interface PageStaticServiceInterface extends BaseServiceInterface
{
    public function getBySlug($slug);
}
