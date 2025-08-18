<?php

namespace App\Repositories\Cart;

use App\Repositories\BaseRepositoryInterface;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserAndProduct(int $userId, int $productId);
    public function getUserActiveCart(int $userId);
    public function clearUserCart(int $userId);
}
