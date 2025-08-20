<?php

namespace App\Repositories\Wishlist;

use App\Repositories\BaseRepositoryInterface;

interface WishlistRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUserId($userId);
    public function insert($userId, $productId);
    public function remove($userId, $productId);
    public function clear($userId);
}
