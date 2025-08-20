<?php

namespace App\Repositories\Wishlists;

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
        return $this->model->where('user_id', $userId)->with('product')->get();
    }

}
