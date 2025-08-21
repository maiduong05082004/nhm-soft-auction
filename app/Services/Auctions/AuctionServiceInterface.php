<?php

namespace App\Services\Auctions;

use App\Services\BaseServiceInterface;

interface AuctionServiceInterface extends BaseServiceInterface
{
    public function getAuctionDetails($productId);
    public function placeBid($auctionId, $userId, $bidPrice);
    public function getAuctionHistory($auctionId);
    public function getUserBidHistory($auctionId, $userId);
    public function validateBid($auctionId, $bidPrice, $userId);
    public function getActiveAuctions();
}
