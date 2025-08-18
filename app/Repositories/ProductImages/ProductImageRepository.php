<?php

namespace App\Repositories\ProductImages;

use App\Models\ProductImage;
use App\Repositories\BaseRepository;

class ProductImageRepository extends BaseRepository
{
    public function getModel(): string
    {
        return ProductImage::class;
    }
}
