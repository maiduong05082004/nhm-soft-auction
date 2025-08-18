<?php

namespace App\Repositories\Products;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductByFilter($query = [], $page = 1, $perPage = 12): LengthAwarePaginator;
    public function incrementViewCount($productId);
}
