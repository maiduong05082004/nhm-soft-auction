<?php

namespace App\Repositories\AuctionBids;

use App\Models\AuctionBid;
use App\Repositories\BaseRepository;

class AuctionBidRepository extends BaseRepository implements AuctionBidRepositoryInterface
{
    public function __construct(AuctionBid $model)
    {
        parent::__construct($model);
    }

    public function getModel(): string
    {
        return AuctionBid::class;
    }
}
