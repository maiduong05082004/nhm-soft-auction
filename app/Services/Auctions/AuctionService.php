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
use Carbon\Carbon;

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
                throw new ServiceException('Không tìm thấy phiên trả giá trực tuyến cho sản phẩm này!');
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
                'message' => 'Có lỗi xảy ra khi lấy thông tin trả giá trực tuyến!'
            ];
        }
    }

    public function placeBid($auctionId, $userId, $bidPrice)
    {

        try {
            DB::beginTransaction();
            // $bidCoin = $this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 0);
            $validation = $this->validateBid($auctionId, $bidPrice, $userId);
            if (!$validation['success']) {
                throw new ServiceException($validation['message']);
            }
            $memberplanActives = auth()->user()['activeMemberships'];
            if (count($memberplanActives) == 0) {
                throw new ServiceException('Cần mua hoặc kích hoạt gói thành viên khác để sử dụng tính năng này!');
            } else if ($memberplanActives[0]['config']['free_auction_participation'] == false) {
                throw new ServiceException('Cần nâng cấp hoặc kích hoạt gói thành viên khác để sử dụng tính năng này!');
            }
            $auction = $validation['auction'];

            // $userHasBidded = $this->bidRepo->query()
            //     ->where('auction_id', $auction->id)
            //     ->where('user_id', $userId)
            //     ->exists();

            // if (!$userHasBidded) {
            //     $user = $this->userRepo->find($userId);
            //     if ($user->current_balance < $bidCoin) {
            //         throw new ServiceException('Số dư của bạn không đủ để tham gia Trả giá.');
            //     }
            //     $user->current_balance -= $bidCoin;
            //     $user->save();
            // }
            // if (!$userHasBidded) {
            //     $coinToDeduct = (int) ($this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 0));
            //     if ($coinToDeduct > 0) {
            //         $this->transactionPointRepo->insertOne([
            //             'point' => -$coinToDeduct,
            //             'description' => 'Phí tham gia Trả giá phiên #' . $auction->id,
            //             'user_id' => $userId,
            //         ]);
            //     }
            // }

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
                'message' => 'Trả giá thành công!',
                'data' => $bid
            ];
        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
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
                ->limit(5)
                ->get();

            return [
                'success' => true,
                'data' => $bids
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy lịch sử trả giá!'
            ];
        }
    }

    public function getUserBidHistory($auctionId, $userId)
    {
        try {
            $userBids = $this->bidRepo->query()
                ->where('auction_id', $auctionId)
                ->where('user_id', $userId)
                ->orderBy('bid_price', 'desc')
                ->limit(3)
                ->get();

            $timeDelay = (int) $this->configService->getConfigValue('TIME_DELAY_AUCTION_BIND', 10);

            $latestUserBid = $this->bidRepo->query()
                ->where('auction_id', $auctionId)
                ->where('user_id', $userId)
                ->orderBy('bid_time', 'desc')
                ->first();

            $nextBidTime = null;
            $canBidNow = true;

            if ($latestUserBid) {
                $nextBidTime = \Carbon\Carbon::parse($latestUserBid->bid_time)->addMinutes($timeDelay);
                $canBidNow = now()->gte($nextBidTime);
            }

            return [
                'success' => true,
                'data' => $userBids,
                'time_delay' => $timeDelay,
                'latest_bid_time' => $latestUserBid ? $latestUserBid->bid_time : null,
                'next_bid_time' => $nextBidTime,
                'can_bid_now' => $canBidNow
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy lịch sử trả giá của user!'
            ];
        }
    }

    public function updateStepPriceByProductId(int $productId, float $stepPrice): bool
    {
        try {
            $auction = $this->auctionRepo->getAuctionByProductId($productId);
            if (!$auction) {
                throw new ServiceException('Không tìm thấy phiên rả giá cho sản phẩm này!');
            }
            $auction->step_price = $stepPrice;t
            return (bool) $auction->save();
        } catch (ServiceException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function validateBid($productId, $bidPrice, $userId)
    {
        try {
            $auction = $this->auctionRepo->getAuctionByProductId($productId);
            if (!$auction) {
                return [
                    'success' => false,
                    'message' => 'Sản phẩm chưa có phiên trả giá!',
                ];
            }

            if (($auction->end_time && now()->gte(\Carbon\Carbon::parse($auction->end_time)))
                || (isset($auction->status) && $auction->status !== 'active')
            ) {
                return [
                    'success' => false,
                    'message' => 'Phiên trả giá đã kết thúc, không thể trả giá thêm!',
                ];
            }

            $latestUserBid = $this->bidRepo->query()
                ->where('auction_id', $auction->id)
                ->where('user_id', $userId)
                ->orderBy('bid_time', 'desc')
                ->first();

            if ($latestUserBid) {
                $timeDelay = (int) $this->configService->getConfigValue('TIME_DELAY_AUCTION_BIND', 10);
                $nextBidTime = Carbon::parse($latestUserBid->bid_time)->addMinutes($timeDelay);

                if (now()->lt($nextBidTime)) {
                    $remainingTime = now()->diffInSeconds($nextBidTime);
                    $remainingMinutes = ceil($remainingTime / 60);

                    return [
                        'success' => false,
                        'message' => "Bạn cần chờ thêm {$remainingMinutes} phút nữa mới có thể trả giá tiếp!",
                    ];
                }
            }

            return [
                'success' => true,
                'auction' => $auction,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra phiên trả giá!'
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
                'message' => 'Có lỗi xảy ra khi lấy danh sách trả giá!'
            ];
        }
    }

    public function getUserParticipatingAuctions($userId)
    {
        try {
            $auctions = $this->auctionRepo->getUserParticipatingAuctions($userId);

            $auctions->each(function($auction) {
                $this->processAuctionData($auction);
            });

            return [
                'success' => true,
                'data' => $auctions
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách sản phẩm trả giá của user!'
            ];
        }
    }

    private function processAuctionData($auction)
    {
        $highestBid = $auction->bids()->orderBy('bid_price', 'desc')->first();
        $currentPrice = $highestBid ? $highestBid->bid_price : $auction->starting_price;
        
        $auction->current_price_display = number_format($currentPrice, 0, ',', '.') . ' ₫';
        
        
        $auction->starting_price_display = number_format($auction->starting_price, 0, ',', '.') . ' ₫';
        
        $auction->highest_bid_display = $highestBid ? number_format($highestBid->bid_price, 0, ',', '.') . ' ₫' : $auction->starting_price_display;
        
        $userBid = $auction->bids()->where('user_id', auth()->id())->orderBy('bid_time', 'desc')->first();
        $auction->user_bid_display = $userBid ? number_format($userBid->bid_price, 0, ',', '.') . ' ₫' : 'Chưa đặt giá';
    }
}
