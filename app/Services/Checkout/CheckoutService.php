<?php

namespace App\Services\Checkout;

use App\Services\BaseService;
use App\Repositories\Orders\OrderDetailRepository;
use App\Repositories\Payments\PaymentRepository;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Users\UserRepository;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService extends BaseService implements CheckoutServiceInterface
{
    protected $orderRepo;
    protected $paymentRepo;
    protected $cartRepo;
    protected $userRepo;

    public function __construct(
        OrderDetailRepository $orderRepo,
        PaymentRepository $paymentRepo,
        CartRepository $cartRepo,
        UserRepository $userRepo
    ) {
        parent::__construct([
            'order' => $orderRepo,
            'payment' => $paymentRepo,
            'cart' => $cartRepo,
            'user' => $userRepo,
        ]);
        
        $this->orderRepo = $orderRepo;
        $this->paymentRepo = $paymentRepo;
        $this->cartRepo = $cartRepo;
        $this->userRepo = $userRepo;
    }

    public function processCheckout(int $userId, array $checkoutData): array
    {
        try {
            DB::beginTransaction();

            $cartResult = $this->cartRepo->getAll(['user_id' => $userId]);
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

            $orderData = [
                'user_id' => $userId,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $total,
                'shipping_address' => $checkoutData['address'],
                'shipping_email' => $checkoutData['email'],
                'shipping_phone' => $checkoutData['phone'] ?? $user->phone,
                'payment_method' => $checkoutData['payment_method'],
                'status' => $checkoutData['payment_method'] == '1' ? 'pending' : 'confirmed',
                'note' => $checkoutData['note'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $order = $this->orderRepo->createOrder($orderData);

            foreach ($cartResult as $cartItem) {
                $orderDetailData = [
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->price * $cartItem->quantity,
                ];
                
                $this->repositories['orderDetail']->create($orderDetailData);
            }

            if ($checkoutData['payment_method'] == '1') {
                $paymentData = [
                    'order_id' => $order->id,
                    'amount' => $total,
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(8)),
                ];
                
                $this->paymentRepo->createPayment($paymentData);
            }

            $this->cartRepo->getAll(['user_id' => $userId])->each(function ($item) {
                $item->delete();
            });

            DB::commit();

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
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
                'data' => null
            ];
        }
    }

    public function getOrderDetails(int $orderId): array
    {
        try {
            $order = $this->orderRepo->getOrderWithDetails($orderId);
            
            if (!$order) {
                throw new ServiceException('Không tìm thấy đơn hàng!');
            }

            $payment = null;
            if ($order->payment_method == '1') {
                $payment = $this->paymentRepo->getPaymentByOrderId($orderId);
            }

            return [
                'success' => true,
                'message' => 'Lấy thông tin đơn hàng thành công!',
                'data' => [
                    'orderDetail' => $order,
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

    public function confirmPayment(int $orderId): array
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepo->getAll(['id' => $orderId])->first();
            if (!$order) {
                throw new ServiceException('Không tìm thấy đơn hàng!');
            }

            if ($order->status !== 'pending') {
                throw new ServiceException('Đơn hàng không ở trạng thái chờ thanh toán!');
            }

            $this->orderRepo->updateOrderStatus($orderId, 'confirmed');

            $payment = $this->paymentRepo->getPaymentByOrderId($orderId);
            if ($payment) {
                $this->paymentRepo->updatePaymentStatus($payment->id, 'completed');
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
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán!',
                'data' => null
            ];
        }
    }

    public function generatePaymentQR(int $orderId): array
    {
        try {
            $order = $this->orderRepo->getOrderWithDetails($orderId);
            
            if (!$order) {
                throw new ServiceException('Không tìm thấy đơn hàng!');
            }

            $payment = $this->paymentRepo->getPaymentByOrderId($orderId);
            if (!$payment) {
                throw new ServiceException('Không tìm thấy thông tin thanh toán!');
            }

            $qrData = [
                'bank_code' => 'VCB',
                'account_number' => '1234567890',
                'account_name' => 'CONG TY ABC',
                'amount' => $payment->amount,
                'description' => 'Thanh toan don hang ' . $order->order_number,
            ];

            return [
                'success' => true,
                'message' => 'Tạo QR code thành công!',
                'data' => [
                    'qr_data' => $qrData,
                    'payment' => $payment,
                    'order' => $order,
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
                'message' => 'Có lỗi xảy ra khi tạo QR code!',
                'data' => null
            ];
        }
    }
}