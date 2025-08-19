<?php

namespace App\Services\Wishlist;

use App\Repositories\Wishlists\WishlistRepository;
use App\Services\BaseService;

class WishlistService extends BaseService implements WishlistServiceInterface
{
    protected $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        parent::__construct(['wishlist' => $wishlistRepository]);
    }

    public function getByUserId($userId)
    {
        return $this->getRepository('wishlist')->getByUserId($userId);
    }

    public function insert($userId, $productId)
    {   
        return $this->getRepository('wishlist')->insert($userId, $productId);
    }

    public function remove($userId, $productId)
    {
        return $this->getRepository('wishlist')->remove($userId, $productId);
    }

    public function clear($userId)
    {
        return $this->getRepository('wishlist')->clear($userId);
    }
}
