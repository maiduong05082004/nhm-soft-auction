<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\BaseRepository;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function getModel(): string
    {
        return Cart::class;
    }

    public function create(array $data)
    {
        return $this->insertOne($data);
    }

    public function update($id, array $data)
    {
        return $this->updateOne($id, $data);
    }

    public function delete($id)
    {
        return $this->deleteOne($id);
    }

    public function findByUserAndProduct(int $userId, int $productId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('status', '1')
            ->first();
    }

    public function getUserActiveCart(int $userId)
    {
        return $this->getAll(
            ['user_id' => $userId, 'status' => '1'],
            ['product', 'product.images']
        );
    }

    public function clearUserCart(int $userId)
    {
        return $this->deleteMany(['user_id' => $userId, 'status' => '1']);
    }
}
