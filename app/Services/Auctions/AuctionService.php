<?php

namespace App\Services\Auctions;

use App\Repositories\Auctions\AuctionRepositoryInterface;
use App\Repositories\AuctionBids\AuctionBidRepositoryInterface;
use App\Services\BaseService;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionService extends BaseService implements AuctionServiceInterface
{
    protected $auctionRepo;
    protected $bidRepo;

    public function __construct(
        AuctionRepositoryInterface $auctionRepo,
        AuctionBidRepositoryInterface $bidRepo
    ) {
        $this->auctionRepo = $auctionRepo;
        $this->bidRepo = $bidRepo;
    }

    public function getAuctionDetails($productId)
    {
        try {
            $auction = $this->auctionRepo->getAuctionByProductId($productId);
            
            if (!$auction) {
                throw new ServiceException('Không tìm thấy phiên đấu giá cho sản phẩm này!');
            }

            $highestBid = $this->bidRepo->query()
                ->where('auction_id', $auction->id)
                ->with('user')
                ->orderBy('bid_price', 'desc')
                ->first();
            $totalBids = $this->bidRepo->query()
                ->where('auction_id', $auction->id)
                ->count();

            return [
                'success' => true,
                'data' => [
                    'auction' => $auction,
                    'highest_bid' => $highestBid,
                    'total_bids' => $totalBids,
                    'current_price' => $highestBid ? $highestBid->bid_price : $auction->start_price,
                    'min_next_bid' => $highestBid ? $highestBid->bid_price + $auction->step_price : $auction->start_price + $auction->step_price
                ]
            ];
        } catch (ServiceException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin đấu giá!'
            ];
        }
    }

    public function placeBid($auctionId, $userId, $bidPrice)
    {
        try {
            DB::beginTransaction();

            $validation = $this->validateBid($auctionId, $bidPrice, $userId);
            if (!$validation['success']) {
                throw new ServiceException($validation['message']);
            }

            $bidData = [
                'auction_id' => $auctionId,
                'user_id' => $userId,
                'bid_price' => $bidPrice,
                'bid_time' => now(),
            ];

            $bid = $this->bidRepo->insertOne($bidData);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Đặt giá thầu thành công!',
                'data' => $bid
            ];

        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt giá thầu!'
            ];
        }
    }

    public function getAuctionHistory($auctionId)
    {
        try {
            $bids = $this->getRepository('bid')->query()
                ->where('auction_id', $auctionId)
                ->with('user')
                ->orderBy('bid_price', 'desc')
                ->get();
            
            return [
                'success' => true,
                'data' => $bids
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy lịch sử đấu giá!'
            ];
        }
    }

    public function validateBid($auctionId, $bidPrice, $userId)
    {
        try {
            $auction = $this->auctionRepo->find($auctionId);
            
            if (!$auction) {
                return ['success' => false, 'message' => 'Không tìm thấy phiên đấu giá!'];
            }

            Log::info('Auction time check:', [
                'auction_id' => $auctionId,
                'current_time' => now()->toDateTimeString(),
                'start_time' => $auction->start_time->toDateTimeString(),
                'end_time' => $auction->end_time->toDateTimeString(),
                'status' => $auction->status
            ]);

            $now = now();
            
            if ($now < $auction->start_time) {
                return ['success' => false, 'message' => 'Phiên đấu giá chưa bắt đầu! (Bắt đầu: ' . $auction->start_time->format('d/m/Y H:i') . ')'];
            }
            
            if ($now > $auction->end_time) {
                return ['success' => false, 'message' => 'Phiên đấu giá đã kết thúc! (Kết thúc: ' . $auction->end_time->format('d/m/Y H:i') . ')'];
            }

            if ($auction->status !== 'active') {
                return ['success' => false, 'message' => 'Phiên đấu giá không hoạt động! (Trạng thái: ' . $auction->status . ')'];
            }

            $highestBid = $this->bidRepo->query()
                ->where('auction_id', $auctionId)
                ->orderBy('bid_price', 'desc')
                ->first();
            $currentPrice = $highestBid ? $highestBid->bid_price : $auction->start_price;
            $minNextBid = $currentPrice + $auction->step_price;

            if ($bidPrice < $minNextBid) {
                return ['success' => false, 'message' => "Giá thầu phải tối thiểu " . number_format($minNextBid, 0, ',', '.') . " ₫!"];
            }

            if ($highestBid && $highestBid->user_id == $userId) {
                return ['success' => false, 'message' => 'Bạn đang là người đặt giá cao nhất!'];
            }

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Auction validation error:', [
                'auction_id' => $auctionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi kiểm tra giá thầu!'];
        }
    }

    public function getActiveAuctions()
    {
        try {
            $auctions = $this->auctionRepo->getActiveAuctions();
            
            return [
                'success' => true,
                'data' => $auctions
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách đấu giá!'
            ];
        }
    }
}
