<?php

namespace App\Services\Wishlist;

use App\Services\BaseServiceInterface;

interface WishlistServiceInterface extends BaseServiceInterface
{
    public function getByUserId($userId);
    public function insert($userId, $productId);
    public function remove($userId, $productId);
    public function clear($userId);
}
