<?php

namespace App\Repositories\Auctions;

use App\Models\Auction;
use App\Repositories\BaseRepository;

class AuctionRepository extends BaseRepository implements AuctionRepositoryInterface
{
    public function __construct(Auction $model)
    {
        parent::__construct($model);
    }

    public function getModel(): string
    {
        return Auction::class;
    }

    public function getActiveAuctions()
    {
        return $this->model->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with(['product', 'bids'])
            ->get();
    }

    public function getAuctionWithBids($auctionId)
    {
        return $this->model->with(['bids.user', 'product'])
            ->find($auctionId);
    }

    public function getHighestBid($auctionId)
    {
        return $this->model->find($auctionId)
            ->bids()
            ->orderBy('bid_price', 'desc')
            ->first();
    }

    public function getBidsByAuction($auctionId)
    {
        return $this->model->find($auctionId)
            ->bids()
            ->with('user')
            ->orderBy('bid_price', 'desc')
            ->get();
    }

    public function getRecentBids($auctionId, int $limit = 10)
    {
        return $this->model->find($auctionId)
            ->bids()
            ->with('user')
            ->orderBy('bid_time', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAuctionByProductId($productId)
    {
        $productIdStr = (string) $productId;
        
        return $this->model->where('product_id', $productIdStr)
            ->with(['bids.user', 'product'])
            ->first();
    }

    public function updateAuctionStatus($auctionId, $status)
    {
        return $this->model->where('id', $auctionId)
            ->update(['status' => $status]);
    }

    public function getUserParticipatingAuctions($userId)
    {
        return $this->model->whereHas('bids', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'active')
            ->where('start_time', '<=', now())
            ->with(['product.images', 'bids' => function($query) use ($userId) {
                $query->where('user_id', $userId)->orderBy('bid_time', 'desc');
            }])
            ->orderBy('end_time', 'desc')
            ->get();
    }
}
