<?php

namespace App\Services\Wishlist;

use App\Services\BaseServiceInterface;

interface WishlistServiceInterface extends BaseServiceInterface
{
    public function getByUserId($userId);
    public function clear($userId);
    public function deleteByUserIdAndProductId($userId, $productId);
    public function createOne($userId, $productId);
    public function getSummary(int $userId): array;
}
