<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function show(Product $product);
    public function incrementViewCount($productId);
    public function filterProductList($query = [], $page = 1, $perPage = 12);
    public function getTreeListCategory();
    public function createProductWithSideEffects(array $data, int $userId): Product;
    public function getCountProductByCreatedByAndNearMonthly($userId) ;
    public function getAuctionStepPriceByProductId(int $productId): ?float;
}
