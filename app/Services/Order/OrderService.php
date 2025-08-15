<?php

namespace App\Services\Order;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Utils\HelperFunc;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService implements OrderServiceInterface
{
    public function processCheckout(int $userId, array $checkoutData): array
    {
        try {
            DB::beginTransaction();

            $cartItems = Cart::with('product')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception('Giỏ hàng trống!');
            }

            $orderDetail = OrderDetail::create([
                'id' => HelperFunc::getTimestampAsId(),
                'code_orders' => 'ORD-' . HelperFunc::getTimestampAsId(),
                'user_id' => $userId,
                'ship_address' => $checkoutData['address'],
                'email_receiver' => $checkoutData['email'],
                'status' => 'pending',
                'subtotal' => $cartItems->sum('total'),
                'shipping_fee' => 30000,
                'total' => $cartItems->sum('total') + 30000,
                'note' => $checkoutData['note'] ?? ''
            ]);

            foreach ($cartItems as $cartItem) {
                Order::create([
                    'id' => HelperFunc::getTimestampAsId(),
                    'order_detail_id' => $orderDetail->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->total,
                    'total' => $cartItem->total
                ]);
            }

            Payment::create([
                'id' => HelperFunc::getTimestampAsId(),
                'order_detail_id' => $orderDetail->id,
                'user_id' => $userId,
                'payment_method' => $checkoutData['payment_method'],
                'amount' => $orderDetail->total,
                'status' => $checkoutData['payment_method'] === '1' ? 'pending' : 'success',
                'pay_date' => $checkoutData['payment_method'] === '0' ? now() : null
            ]);
            
            Cart::where('user_id', $userId)
                ->where('status', 'active')
                ->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_id' => $orderDetail->id,
                    'payment_method' => $checkoutData['payment_method']
                ]
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getOrderDetails(int $orderId): array
    {
        try {
            $orderDetail = OrderDetail::with(['payments', 'user'])->findOrFail($orderId);
            $payment = $orderDetail->payments->first();

            return [
                'success' => true,
                'message' => 'Lấy thông tin đơn hàng thành công!',
                'data' => [
                    'orderDetail' => $orderDetail,
                    'payment' => $payment
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function confirmPayment(int $orderId): array
    {
        try {
            DB::beginTransaction();

            $orderDetail = OrderDetail::findOrFail($orderId);
            $payment = $orderDetail->payments->first();

            if (!$payment) {
                throw new Exception('Không tìm thấy thông tin thanh toán!');
            }

            $payment->update([
                'status' => 'success',
                'pay_date' => now()
            ]);

            $orderDetail->update(['status' => 'paid']);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Xác nhận thanh toán thành công!',
                'data' => [
                    'order_id' => $orderId
                ]
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
