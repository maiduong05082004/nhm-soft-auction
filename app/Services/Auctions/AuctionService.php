<?php

namespace App\Services\Auctions;

use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\AuctionBids\AuctionBidRepository;
use App\Repositories\TransactionPoint\TransactionPointRepository;
use App\Services\Config\ConfigService;
use App\Services\BaseService;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Users\UserRepository;

class AuctionService extends BaseService implements AuctionServiceInterface
{
    protected $auctionRepo;
    protected $bidRepo;
    protected $transactionPointRepo;
    protected $configService;
    protected $userRepo;
    public function __construct(
        AuctionRepository $auctionRepo,
        AuctionBidRepository $bidRepo,
        TransactionPointRepository $transactionPointRepo,
        ConfigService $configService,
        UserRepository $userRepo
    ) {
        $this->auctionRepo = $auctionRepo;
        $this->bidRepo = $bidRepo;
        $this->transactionPointRepo = $transactionPointRepo;
        $this->configService = $configService;
        $this->userRepo = $userRepo;
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
            $bidCoin = $this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 0);
            $validation = $this->validateBid($auctionId, $bidPrice, $userId);
            if (!$validation['success']) {
                throw new ServiceException($validation['message']);
            }

            $auction = $validation['auction'];

            $userHasBidded = $this->bidRepo->query()
                ->where('auction_id', $auction->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$userHasBidded) {
                $user = $this->userRepo->find($userId);
                if ($user->current_balance < $bidCoin) {
                    throw new ServiceException('Số dư của bạn không đủ để tham gia đấu giá.');
                }
                $user->current_balance -= $bidCoin;
                $user->save();
            }
            if (!$userHasBidded) {
                $coinToDeduct = (int) ($this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 0));
                if ($coinToDeduct > 0) {
                    $this->transactionPointRepo->insertOne([
                        'point' => -$coinToDeduct,
                        'description' => 'Phí tham gia đấu giá phiên #' . $auction->id,
                        'user_id' => $userId,
                    ]);
                }
            }

            $bidData = [
                'auction_id' => $auction->id,
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

    public function validateBid($productId, $bidPrice, $userId)
    {
        try {
            $auction = $this->auctionRepo->getAuctionByProductId($productId);
            if (!$auction) {
                return [
                    'success' => false,
                    'message' => 'Sản phẩm chưa có phiên đấu giá!',
                ];
            }

            return [
                'success' => true,
                'auction' => $auction,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra phiên đấu giá!'
            ];
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
