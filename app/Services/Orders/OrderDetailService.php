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

class OrderDetailService extends BaseService implements OrderDetailServiceInterface
{
    public function __construct(OrderDetailRepository $orderDetailRepo, ProductRepository $productRepo)
    {
        parent::__construct([
            'orderDetail' => $orderDetailRepo,
            'product' => $productRepo,
        ]);
    }

    public function getAll()
    {
        return $this->getRepository('orderDetail')->getAll();
    }

    public function getById($id)
    {
        return $this->getRepository('orderDetail')->find($id);
    }

    public function create(array $data)
    {
        return $this->createOrder($data);
    }

    public function update($id, array $data)
    {
        return $this->updateOrder($id, $data);
    }

    public function delete($id)
    {
        return $this->getRepository('orderDetail')->deleteOne($id);
    }

    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $productId = $item['product_id'] ?? null;
            $price = $productId ? (float) (Product::find($productId)?->price ?? 0) : (float) ($item['price'] ?? 0);
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
        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);

        return $this->getRepository('orderDetail')->insertOne($data);
    }

    public function updateOrder($id, array $data)
    {
        $data['subtotal'] = $this->calculateSubtotal($data['items'] ?? []);
        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);

        return $this->getRepository('orderDetail')->updateOne($id, $data);
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
        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
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
}
