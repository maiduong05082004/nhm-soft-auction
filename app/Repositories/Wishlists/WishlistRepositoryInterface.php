<?php

namespace App\Repositories\Wishlists;

use App\Repositories\BaseRepositoryInterface;

interface WishlistRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUserId($userId);
}
