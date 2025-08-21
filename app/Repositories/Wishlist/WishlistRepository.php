<?php

namespace App\Repositories\Wishlist;

use App\Models\Wishlist;
use App\Repositories\BaseRepository;

class WishlistRepository extends BaseRepository implements WishlistRepositoryInterface
{
    public function getModel(): string
    {
        return Wishlist::class;
    }

    public function getByUserId($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with([
                'product' => function ($query) {
                    $query->with('firstImage');
                }
            ])
            ->get();
    }

    public function insert($userId, $productId)
    {
        return $this->model->firstOrCreate([
            'user_id'    => $userId,
            'product_id' => $productId
        ]);
    }

    public function remove($userId, $productId)
    {
        return $this->model->where('user_id', $userId)
            ->where('product_id', intval($productId))
            ->delete();
    }

    public function clear($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }
}
