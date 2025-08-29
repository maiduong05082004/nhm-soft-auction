<?php

namespace App\Services\Checkout;

use App\Services\BaseService;
use App\Enums\OrderStatus;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\OrderDetails\OrderDetailRepository;
use App\Repositories\Payments\PaymentRepository;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\AuctionBids\AuctionBidRepository;
use App\Exceptions\ServiceException;
use App\Repositories\CreditCards\CreditCardRepository;
use App\Utils\HelperFunc;
use Illuminate\Support\Facades\DB;
use App\Services\Products\ProductService;

class CheckoutService extends BaseService implements CheckoutServiceInterface
{
    protected $orderRepo;
    protected $paymentRepo;
    protected $cartRepo;
    protected $userRepo;
    protected $creditCardRepo;
    protected $auctionRepo;
    protected $auctionBidRepo;
    protected $productService;

    public function __construct(
        OrderRepository $orderRepo,
        OrderDetailRepository $orderDetailRepo,
        PaymentRepository $paymentRepo,
        CartRepository $cartRepo,
        UserRepository $userRepo,
        ProductRepository $productRepo,
        CreditCardRepository $creditCardRepo,
        AuctionRepository $auctionRepo,
        AuctionBidRepository $auctionBidRepo,
        ProductService $productService
    ) {
        parent::__construct([
            'order' => $orderRepo,
            'orderDetail' => $orderDetailRepo,
            'payment' => $paymentRepo,
            'cart' => $cartRepo,
            'user' => $userRepo,
            'product' => $productRepo,
            'creditCard' => $creditCardRepo,
            'auction' => $auctionRepo,
            'bid' => $auctionBidRepo,
        ]);
        
        $this->orderRepo = $orderRepo;
        $this->paymentRepo = $paymentRepo;
        $this->cartRepo = $cartRepo;
        $this->userRepo = $userRepo;
        $this->creditCardRepo = $creditCardRepo;
        $this->auctionRepo = $auctionRepo;
        $this->auctionBidRepo = $auctionBidRepo;
        $this->productService = $productService;
    }

    public function processCheckout(int $userId, array $checkoutData): array
    {
        try {
            DB::beginTransaction();

            $cartResult = $this->cartRepo->getAll(['user_id' => $userId]);
            if (!empty($checkoutData['selected'])) {
                $ids = collect(explode(',', (string) $checkoutData['selected']))->filter()->map(fn($v) => (int) $v)->all();
                if (!empty($ids)) {
                    $cartResult = $cartResult->whereIn('product_id', $ids);
                }
            }
            if ($cartResult->isEmpty()) {
                throw new ServiceException('Giỏ hàng trống!');
            }
            $user = $this->userRepo->getAll(['id' => $userId])->first();
            if (!$user) {
                throw new ServiceException('Không tìm thấy thông tin người dùng!');
            }

            $total = $cartResult->sum(function ($item) {
                $product = $this->repositories['product']->find($item->product_id);
                if ($product) {
                    $currentPrice = $this->productService->getCurrentProductPrice($product);
                    return $currentPrice * $item->quantity;
                }
                return $item->price * $item->quantity;
            });
            $orderDetailStatus = $checkoutData['payment_method'] == '1' ? 1 : 2;
            $shippingFee = 0;
            $subtotal = $total + $shippingFee;
            
            $discountInfo = $this->getCheckoutDiscountInfo($userId, $subtotal);
            $finalTotal = $discountInfo['final_total'];
            $orderDetailData = [
                'code_orders' => 'ORD' . HelperFunc::getTimestampAsId(),
                'user_id' => $userId,
                'email_receiver' => $checkoutData['email'],
                'ship_address' => $checkoutData['address'],
                'payment_method' => $checkoutData['payment_method'],
                'shipping_fee' => $shippingFee,
                'discount_percentage' => $discountInfo['discount_percentage'],
                'subtotal' => $subtotal,
                'total' => $finalTotal,
                'note' => $checkoutData['note'] ?? '',
                'status' => $orderDetailStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderDetail = $this->getRepository('orderDetail')->insertOne($orderDetailData);
            
            foreach ($cartResult as $cartItem) {
                $product = $this->repositories['product']->find($cartItem->product_id);
                $currentPrice = $product ? $this->productService->getCurrentProductPrice($product) : $cartItem->price;
                
                $orderData = [
                    'product_id' => $cartItem->product_id,
                    'order_detail_id' => $orderDetail->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $currentPrice,
                    'total' => $currentPrice * $cartItem->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->getRepository('order')->insertOne($orderData);
                $this->getRepository('product')->updateMany(
                    ['id' => $cartItem->product_id],
                    ['stock' => DB::raw('GREATEST(stock - ' . (int) $cartItem->quantity . ', 0)')]
                );
            }

                $paymentData = [
                    'user_id' => $userId,
                    'order_detail_id' => $orderDetail->id,
                    'amount' => $finalTotal,
                    'payment_method' => $checkoutData['payment_method'],
                    'status' => 'pending',
                    'transaction_id' => 'TXN-' . HelperFunc::getTimestampAsId(),
                    'payer_id' => HelperFunc::getTimestampAsId(),
                    'pay_date' => now(),
                    'currency_code' => 'VND',
                    'payer_email' => $checkoutData['email'],
                    'transaction_fee' => 0,
                    'status' => $checkoutData['payment_method'] === '1' ? 'pending' : 'success',
                ];
                
                $this->getRepository('payment')->insertOne($paymentData);            

            $cartResult->each(function ($item) {
                $item->delete();
            });

            DB::commit();
            return [
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_detail_id' => $orderDetail->id,
                    'code_orders' => $orderDetail->code_orders,
                ]
            ];

        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý đơn hàng!',
                'data' => $e->getMessage()
            ];
        }
    }

    public function getOrderDetails(int $orderId): array
    {
        try {
            $orderDetail = $this->getRepository('orderDetail')->find($orderId);
            if (!$orderDetail) {
                throw new ServiceException('Không tìm thấy đơn hàng!');
            }

            $payment = null;
            if ($orderDetail->payment_method == '1') {
                $payment = $this->getRepository('payment')->getAll(['order_detail_id' => $orderId])->first();
            }

            return [
                'success' => true,
                'message' => 'Lấy thông tin đơn hàng thành công!',
                'data' => [
                    'orderDetail' => $orderDetail,
                    'payment' => $payment,
                ]
            ];

        } catch (ServiceException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin đơn hàng!',
                'data' => null
            ];
        }
    }

    public function hasCreditCardConfig(): bool
    {
        return (bool) $this->creditCardRepo->getAll([])->first();
    }

    public function buildVietQrUrl(object $orderDetail, ?object $payment): string
    {
        $creditCard = $this->creditCardRepo->getAll([])->first();
        if (!$creditCard) {
            return '';
        }
        $vietqrUrl = 'https://img.vietqr.io/image/' . $creditCard->bin_bank . '-' . $creditCard->card_number . '-compact2.jpg';
        $vietqrUrl .= '?amount=' . ($payment->amount ?? 0);
        $vietqrUrl .= '&addInfo=' . urlencode('Thanh toan don hang ' . ($orderDetail->code_orders ?? ''));
        $vietqrUrl .= '&accountName=' . urlencode($creditCard->name);
        return $vietqrUrl;
    }

    public function processAuctionWinnerPayment(int $userId, int $auctionId): array
    {
        try {
            $auction = $this->auctionRepo->getAll(['id' => $auctionId])->first();
            if (!$auction) {
                throw new ServiceException('Không tìm thấy phiên đấu giá!');
            }

            if (!$auction->end_time || now()->lt(\Carbon\Carbon::parse($auction->end_time))) {
                throw new ServiceException('Phiên đấu giá chưa kết thúc!');
            }

            $winnerBid = $this->auctionBidRepo->query()
                ->where('auction_id', $auctionId)
                ->orderBy('bid_price', 'desc')
                ->first();
            if (!$winnerBid) {
                throw new ServiceException('Chưa có người thắng cuộc cho phiên này!');
            }

            if ((int)$winnerBid->user_id !== (int)$userId) {
                throw new ServiceException('Bạn không phải người thắng phiên đấu giá này!');
            }

            $user = $this->userRepo->getAll(['id' => $userId])->first();
            if (!$user) {
                throw new ServiceException('Không tìm thấy người dùng!');
            }

            return [
                'success' => true,
                'message' => 'Chuyển đến trang thanh toán!',
                'data' => [
                    'auction_id' => $auctionId,
                    'product_id' => $auction->product_id,
                    'amount' => $winnerBid->bid_price,
                    'redirect_to' => 'checkout',
                    'checkout_data' => [
                        'auction_id' => $auctionId,
                        'product_id' => $auction->product_id,
                        'quantity' => 1,
                        'amount' => $winnerBid->bid_price,
                        'name' => $user->name ?? '',
                        'email' => $user->email ?? '',
                        'phone' => $user->phone ?? '',
                        'address' => $user->address ?? '',
                        'note' => 'Thanh toán sản phẩm đấu giá #' . $auction->id,
                    ]
                ],
            ];

        } catch (ServiceException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khởi tạo thanh toán!',
                'data' => null,
            ];
        }
    }

    public function processAuctionCheckout(int $userId, array $checkoutData): array
    {
        try {
            DB::beginTransaction();

            $auction = $this->auctionRepo->getAll(['id' => $checkoutData['auction_id']])->first();
            if (!$auction) {
                throw new ServiceException('Không tìm thấy phiên đấu giá!');
            }

            $winnerBid = $this->auctionBidRepo->query()
                ->where('auction_id', $checkoutData['auction_id'])
                ->orderBy('bid_price', 'desc')
                ->first();
            if (!$winnerBid || (int)$winnerBid->user_id !== (int)$userId) {
                throw new ServiceException('Bạn không phải người thắng phiên đấu giá này!');
            }

            $user = $this->userRepo->getAll(['id' => $userId])->first();
            if (!$user) {
                throw new ServiceException('Không tìm thấy thông tin người dùng!');
            }

            $amount = $winnerBid->bid_price;
            $orderDetailStatus = $checkoutData['payment_method'] == '1' ? 1 : 2; // 1=pending, 2=processing
            
            $discountInfo = $this->getCheckoutDiscountInfo($userId, $amount);
            $finalAmount = $discountInfo['final_total'];
            
            $orderDetailData = [
                'code_orders' => 'ORD' . HelperFunc::getTimestampAsId(),
                'user_id' => $userId,
                'email_receiver' => $checkoutData['email'] ?? $user->email,
                'ship_address' => $checkoutData['address'] ?? $user->address,
                'payment_method' => $checkoutData['payment_method'],
                'shipping_fee' => 0,
                'discount_percentage' => $discountInfo['discount_percentage'],
                'subtotal' => $amount,
                'total' => $finalAmount,
                'note' => $checkoutData['note'] ?? 'Thanh toán sản phẩm đấu giá #' . $auction->id,
                'status' => $orderDetailStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderDetail = $this->getRepository('orderDetail')->insertOne($orderDetailData);

            $orderData = [
                'product_id' => $auction->product_id,
                'order_detail_id' => $orderDetail->id,
                'quantity' => 1,
                'price' => $amount,
                'total' => $finalAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $this->getRepository('order')->insertOne($orderData);

            $this->getRepository('product')->updateMany(
                ['id' => $auction->product_id],
                ['stock' => DB::raw('GREATEST(stock - 1, 0)')]
            );

            $paymentStatus = $checkoutData['payment_method'] === '1' ? 'pending' : 'success';
            $paymentData = [
                'user_id' => $userId,
                'order_detail_id' => $orderDetail->id,
                'amount' => $finalAmount,
                'payment_method' => $checkoutData['payment_method'],
                'status' => $paymentStatus,
                'transaction_id' => 'TXN-' . HelperFunc::getTimestampAsId(),
                'payer_id' => HelperFunc::getTimestampAsId(),
                'pay_date' => now(),
                'currency_code' => 'VND',
                'payer_email' => $checkoutData['email'] ?? $user->email,
                'transaction_fee' => 0,
            ];
            $this->getRepository('payment')->insertOne($paymentData);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_detail_id' => $orderDetail->id,
                    'code_orders' => $orderDetail->code_orders,
                    'payment_status' => $paymentStatus,
                ]
            ];

        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý đơn hàng!',
                'data' => $e->getMessage()
            ];
        }
    }

    public function confirmPayment(int $orderId): array
    {
        try {
            DB::beginTransaction();

            $orderDetail = $this->getRepository('orderDetail')->find($orderId);
            if (!$orderDetail) {
                throw new ServiceException('Không tìm thấy đơn hàng!');
            }
            if (($orderDetail->status instanceof OrderStatus ? $orderDetail->status->value : (int) $orderDetail->status) !== OrderStatus::New->value) {
                throw new ServiceException('Đơn hàng không ở trạng thái chờ thanh toán!');
            }

            $this->getRepository('orderDetail')->updateOne($orderId, ['status' => OrderStatus::Processing->value]);

            $payment = $this->getRepository('payment')->getAll(['order_detail_id' => $orderId])->first();
            if ($payment) {
                $this->getRepository('payment')->updateOne($payment->id, ['status' => 'success']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Xác nhận thanh toán thành công!',
                'data' => [
                    'order_id' => $orderId,
                ]
            ];

        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getUserMembershipDiscount(object $user): array
    {
        $activeMembership = $user->activeMemberships()
            ->where('end_date', '>', now())
            ->first();

        if (!$activeMembership) {
            return [
                'has_discount' => false,
                'discount_percentage' => 0,
                'membership_name' => null
            ];
        }

        $discountPercentage = $activeMembership->getDiscountPercentage();
        
        return [
            'has_discount' => $discountPercentage > 0,
            'discount_percentage' => $discountPercentage,
            'membership_name' => $activeMembership->name,
            'membership_plan' => $activeMembership
        ];
    }


    public function getCheckoutDiscountInfo(int $userId, float $subtotal): array
    {
        $user = $this->userRepo->find($userId);
        if (!$user) {
            return [
                'has_discount' => false,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'final_total' => $subtotal,
                'membership_name' => null
            ];
        }

        $discountInfo = $this->getUserMembershipDiscount($user);
        
        if ($discountInfo['has_discount']) {
            $discountAmount = ($subtotal * $discountInfo['discount_percentage']) / 100;
            $finalTotal = $subtotal - $discountAmount;
            
            return array_merge($discountInfo, [
                'discount_amount' => round($discountAmount, 0),
                'final_total' => round($finalTotal, 0)
            ]);
        }
        
        return array_merge($discountInfo, [
            'discount_amount' => 0,
            'final_total' => $subtotal
        ]);
    }
}