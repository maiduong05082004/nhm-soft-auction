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
use Illuminate\Support\Str;

class CheckoutService extends BaseService implements CheckoutServiceInterface
{
    protected $orderRepo;
    protected $paymentRepo;
    protected $cartRepo;
    protected $userRepo;
    protected $creditCardRepo;
    protected $auctionRepo;
    protected $auctionBidRepo;

    public function __construct(
        OrderRepository $orderRepo,
        OrderDetailRepository $orderDetailRepo,
        PaymentRepository $paymentRepo,
        CartRepository $cartRepo,
        UserRepository $userRepo,
        ProductRepository $productRepo,
        CreditCardRepository $creditCardRepo,
        AuctionRepository $auctionRepo,
        AuctionBidRepository $auctionBidRepo
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
                return $item->price * $item->quantity;
            });
            $orderDetailStatus = $checkoutData['payment_method'] == '1' ? 1 : 2;
            $shippingFee = 0;
            $subtotal = $total+$shippingFee;
            $orderDetailData = [
                'code_orders' => 'ORD-' . HelperFunc::getTimestampAsId(),
                'user_id' => $userId,
                'email_receiver' => $checkoutData['email'],
                'ship_address' => $checkoutData['address'],
                'payment_method' => $checkoutData['payment_method'],
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'total' => $total,
                'note' => $checkoutData['note'] ?? '',
                'status' => $orderDetailStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderDetail = $this->getRepository('orderDetail')->insertOne($orderDetailData);
            
            foreach ($cartResult as $cartItem) {
                $orderData = [
                    'product_id' => $cartItem->product_id,
                    'order_detail_id' => $orderDetail->id,
                    'quantity' => $cartItem->quantity,
                    'total' => $cartItem->price * $cartItem->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->getRepository('order')->insertOne($orderData);
                $this->getRepository('product')->updateMany(
                    ['id' => $cartItem->product_id],
                    ['stock' => DB::raw('GREATEST(stock - ' . (int) $cartItem->quantity . ', 0)')]
                );
            }

            if ($checkoutData['payment_method'] == '1') {
                $paymentData = [
                    'user_id' => $userId,
                    'order_detail_id' => $orderDetail->id,
                    'amount' => $total,
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
            }

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
            DB::beginTransaction();

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

            $hasCredit = (bool) $this->creditCardRepo->getAll([])->first();
            if (!$hasCredit) {
                throw new ServiceException('Chưa cấu hình tài khoản nhận thanh toán (VietQR)!');
            }

            $user = $this->userRepo->getAll(['id' => $userId])->first();
            if (!$user) {
                throw new ServiceException('Không tìm thấy người dùng!');
            }

            $amount = $winnerBid->bid_price;
            $orderDetailData = [
                'code_orders' => 'ORD-' . HelperFunc::getTimestampAsId(),
                'user_id' => $userId,
                'email_receiver' => $user->email ?? '',
                'ship_address' => $user->address ?? '',
                'payment_method' => '1',
                'shipping_fee' => 0,
                'subtotal' => $amount,
                'total' => $amount,
                'note' => 'Thanh toán sản phẩm đấu giá #' . $auction->id,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderDetail = $this->getRepository('orderDetail')->insertOne($orderDetailData);

            $orderData = [
                'product_id' => $auction->product_id,
                'order_detail_id' => $orderDetail->id,
                'quantity' => 1,
                'total' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $this->getRepository('order')->insertOne($orderData);

            $this->getRepository('product')->updateMany(
                ['id' => $auction->product_id],
                ['stock' => DB::raw('GREATEST(stock - 1, 0)')]
            );

            $paymentData = [
                'user_id' => $userId,
                'order_detail_id' => $orderDetail->id,
                'amount' => $amount,
                'payment_method' => '1',
                'status' => 'pending',
                'transaction_id' => 'TXN-' . HelperFunc::getTimestampAsId(),
                'payer_id' => HelperFunc::getTimestampAsId(),
                'pay_date' => now(),
                'currency_code' => 'VND',
                'payer_email' => $user->email ?? '',
                'transaction_fee' => 0,
            ];
            $this->getRepository('payment')->insertOne($paymentData);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Khởi tạo thanh toán thành công!',
                'data' => [
                    'order_detail_id' => $orderDetail->id,
                    'code_orders' => $orderDetail->code_orders,
                ],
            ];
        } catch (ServiceException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khởi tạo thanh toán!',
                'data' => null,
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
}