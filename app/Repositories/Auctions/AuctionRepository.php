<?php

namespace App\Repositories\Auctions;

use App\Models\Auction;
use App\Repositories\BaseRepository;

class AuctionRepository extends BaseRepository
{
    public function getModel(): string
    {
        return Auction::class;
    }
}
