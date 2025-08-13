<?php

namespace App\Repositories\Products;

use App\Repositories\BaseRepository;
use App\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel(): string
    {
        return Product::class;
    }
}