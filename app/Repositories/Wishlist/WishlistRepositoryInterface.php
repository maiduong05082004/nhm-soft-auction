<?php

namespace App\Repositories\Wishlist;

use App\Repositories\BaseRepositoryInterface;

interface WishlistRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUserId($userId);
}
