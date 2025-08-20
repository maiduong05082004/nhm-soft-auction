<?php

namespace App\Services\Wishlist;

use App\Exceptions\ServiceException;
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

    public function deleteByUserIdAndProductId($userId, $productId)
    {
        return $this->getRepository('wishlist')->query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clear($userId)
    {
        return $this->getRepository('wishlist')->deleteMany(['user_id' => $userId]);
    }

    public function createOne($userId, $productId)
    {
        $repo = $this->getRepository('wishlist');

        $exists = $repo->query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            throw new ServiceException('Sản phẩm đã có trong wishlist');
        }

        return $repo->insertOne([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }
}
