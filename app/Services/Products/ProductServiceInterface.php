<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Services\BaseServiceInterface;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function show(Product $product);
}
