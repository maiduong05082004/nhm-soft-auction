<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Models\Product;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $orderRepo, ProductRepository $productRepo)
    {
        parent::__construct([
            'order' => $orderRepo,
            'product' => $productRepo,
        ]);
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

        return $this->getRepository('order')->insertOne($data);
    }

    public function updateOrder($id, array $data)
    {
        $data['subtotal'] = $this->calculateSubtotal($data['items'] ?? []);
        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
        $data['total'] = $this->calculateTotal($data['items'] ?? [], $shippingFee);

        return $this->getRepository('order')->updateOne($id, $data);
    }

    public function getOrderById($id)
    {
        return $this->getRepository('order')->find($id);
    }

    public function getAllOrders(array $conditions = [])
    {
        return $this->getRepository('order')->getAll($conditions);
    }
}