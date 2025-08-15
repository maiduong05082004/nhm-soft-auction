<?php

namespace App\Repositories\Cart;

interface CartRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function findByUserAndProduct(int $userId, int $productId);
    public function getUserActiveCart(int $userId);
    public function clearUserCart(int $userId);
}
