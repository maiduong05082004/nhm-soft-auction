<?php

namespace App\Services\Orders;

use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Services\BaseService;
use App\Repositories\Orders\OrderDetailRepository;
use App\Repositories\Products\ProductRepository;
use App\Services\Orders\OrderDetailServiceInterface;
use App\Utils\HelperFunc;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;

class OrderDetailService extends BaseService implements OrderDetailServiceInterface
{
    public function __construct(OrderDetailRepository $orderDetailRepo, ProductRepository $productRepo)
    {
        parent::__construct([
            'orderDetail' => $orderDetailRepo,
            'product' => $productRepo,
        ]);
    }

    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $quantity = (float)($item['quantity'] ?? 0);
            $productId = $item['product_id'] ?? null;
            $price = $productId ? (float)(Product::find($productId)?->price ?? 0) : (float)($item['price'] ?? 0);
            $subtotal += $quantity * $price;
        }
        return $subtotal;
    }

    public function calculateTotal(array $items, float $shippingFee = 0): float
    {
        $subtotal = $this->calculateSubtotal($items);
        return $subtotal + $shippingFee;
    }

    public function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }

    public function createOrder(array $data)
    {
        $data['subtotal'] = $this->calculateSubtotal($data['items'] ?? []);
        $shippingFee = (float)($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);

        return $this->create('orderDetail', $data);
    }

    public function updateOrder($id, array $data)
    {
        $data['subtotal'] = $this->calculateSubtotal($data['items'] ?? []);
        $shippingFee = (float)($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);
        return $this->update('orderDetail', $id, $data);
    }

    public function getOrderById($id)
    {
        return $this->getRepository('orderDetail')->find($id);
    }

    public function getAllOrders(array $conditions = [])
    {
        return $this->getRepository('orderDetail')->getAll($conditions);
    }

    public function calculateOrderTotals(array $data): array
    {
        $data['subtotal'] = $this->calculateSubtotal($data['items'] ?? []);
        $shippingFee = (float)($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);
        return $data;
    }

    public function afterCreate(OrderDetail $orderDetail, string $paymentMethod): void
    {
        $orderDetail->loadMissing('items', 'items.product');
        $subtotal = 0.0;

        foreach ($orderDetail->items as $item) {
            $qty = (float) ($item->quantity ?? 0);
            $price = (float) ($item->product?->price ?? 0);
            $subtotal += $qty * $price;
        }

        $shippingFee = (float) ($orderDetail->shipping_fee ?? 0);

        $orderDetail->forceFill([
            'subtotal' => $subtotal,
            'total' => $subtotal + $shippingFee,
        ])->save();

        Payment::create([
            'order_detail_id' => $orderDetail->id,
            'user_id' => $orderDetail->user_id,
            'payment_method' => $paymentMethod,
            'amount' => $orderDetail->total,
            'transaction_id' => HelperFunc::getTimestampAsId(),
            'payer_id' => HelperFunc::getTimestampAsId(),
            'pay_date' => now(),
            'currency_code' => 'VND',
            'payer_email' => $orderDetail->email_receiver,
            'transaction_fee' => 0,
            'status' => $paymentMethod === '1' ? 'pending' : 'success',
        ]);
    }
    
    public function processCheckout(int $userId, array $checkoutData): array
    {
        try {
            DB::beginTransaction();

            $cartItems = Cart::with('product')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->get();

            if ($cartItems->isEmpty()) {
                throw new ServiceException('Giỏ hàng trống!');
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

            $orderDetail = OrderDetail::findOrFail($orderId);
            $payment = $orderDetail->payments->first();

            if (!$payment) {
                throw new ServiceException('Không tìm thấy thông tin thanh toán!');
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
}
