<?php

namespace App\Repositories\Auctions;

use App\Repositories\BaseRepositoryInterface;

interface AuctionRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveAuctions();
    public function getAuctionWithBids($auctionId);
    public function getHighestBid($auctionId);
    public function getAuctionByProductId($productId);
    public function updateAuctionStatus($auctionId, $status);
}
