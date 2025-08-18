<?php

namespace App\Services\Cart;

use App\Services\BaseServiceInterface;

interface CartServiceInterface extends BaseServiceInterface
{
    public function addToCart(int $userId, int $productId, int $quantity): array;
    public function getUserCart(int $userId): array;
    public function updateQuantity(int $userId, int $productId, int $quantity): array;
    public function removeItem(int $userId, int $productId): array;
    public function clearCart(int $userId): array;
    public function getCartSummary(int $userId): array;
}
