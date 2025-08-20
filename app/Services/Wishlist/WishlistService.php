<?php

namespace App\Services\Wishlist;

use App\Repositories\Wishlist\WishlistRepository;
use App\Services\BaseService;

class WishlistService extends BaseService implements WishlistServiceInterface
{
    protected $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        parent::__construct([
            'wishlist' => $wishlistRepository
        ]);
        $this->wishlistRepository = $wishlistRepository;
    }

    public function getByUserId($userId)
    {
        return $this->wishlistRepository->getByUserId($userId);
    }

    public function insert($userId, $productId)
    {   
        return $this->wishlistRepository->insert($userId, $productId);
    }

    public function remove($userId, $productId)
    {
        return $this->wishlistRepository->remove($userId, $productId);
    }

    public function clear($userId)
    {
        return $this->wishlistRepository->clear($userId);
    }
}
