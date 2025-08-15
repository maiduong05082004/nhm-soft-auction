<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CartService implements CartServiceInterface
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function addToCart(int $userId, int $productId, int $quantity): array
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            
            if ($product->status !== 'active') {
                throw new Exception('Sản phẩm không khả dụng!');
            }
            
            if ($product->stock < $quantity) {
                throw new Exception('Số lượng vượt quá tồn kho!');
            }

            $existingCart = $this->cartRepository->findByUserAndProduct($userId, $productId);

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $quantity;
                if ($product->stock < $newQuantity) {
                    throw new Exception('Số lượng vượt quá tồn kho!');
                }

                $this->cartRepository->update($existingCart->id, [
                    'quantity' => $newQuantity,
                    'total' => $newQuantity * $existingCart->price
                ]);
            } else {
                $this->cartRepository->create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total' => $quantity * $product->price,
                    'status' => 1
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng!',
                'data' => null
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

    public function getUserCart(int $userId): array
    {
        try {
            $cartItems = $this->cartRepository->getUserActiveCart($userId);
            
            $validCartItems = $cartItems->filter(function ($cartItem) {
                return $cartItem->product && $cartItem->product->exists;
            });
            
            $total = $validCartItems->sum('total');

            return [
                'success' => true,
                'message' => 'Lấy giỏ hàng thành công!',
                'data' => [
                    'cartItems' => $validCartItems,
                    'total' => $total,
                    'count' => $validCartItems->count()
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

    public function updateQuantity(int $userId, int $productId, int $quantity): array
    {
        try {
            if ($quantity < 1) {
                throw new Exception('Số lượng phải lớn hơn 0!');
            }

            $product = Product::where('id', $productId)
                            ->where('status', 'active')
                            ->whereNull('deleted_at')
                            ->first();
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại hoặc không khả dụng!');
            }
            
            if ($product->stock < $quantity) {
                throw new Exception('Số lượng vượt quá tồn kho!');
            }

            $cartItem = $this->cartRepository->findByUserAndProduct($userId, $productId);
            if (!$cartItem) {
                throw new Exception('Không tìm thấy sản phẩm trong giỏ hàng!');
            }

            $this->cartRepository->update($cartItem->id, [
                'quantity' => $quantity,
                'total' => $quantity * $cartItem->price
            ]);
            
            $updatedCartItem = $this->cartRepository->find($cartItem->id);
            $cartSummary = $this->getCartSummary($userId);

            return [
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!',
                'data' => [
                    'quantity' => $updatedCartItem->quantity,
                    'total' => $updatedCartItem->total,
                    'cart_total' => $cartSummary['data']['total'],
                    'cart_count' => $cartSummary['data']['count']
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

    public function removeItem(int $userId, int $productId): array
    {
        try {
            $cartItem = $this->cartRepository->findByUserAndProduct($userId, $productId);
            if (!$cartItem) {
                throw new Exception('Không tìm thấy sản phẩm trong giỏ hàng!');
            }

            $this->cartRepository->delete($cartItem->id);
            
            $cartSummary = $this->getCartSummary($userId);

            return [
                'success' => true,
                'message' => 'Xóa sản phẩm thành công!',
                'data' => [
                    'cart_total' => $cartSummary['data']['total'],
                    'cart_count' => $cartSummary['data']['count']
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

    public function clearCart(int $userId): array
    {
        try {
            $this->cartRepository->clearUserCart($userId);

            return [
                'success' => true,
                'message' => 'Xóa giỏ hàng thành công!',
                'data' => [
                    'cart_total' => 0,
                    'cart_count' => 0
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

    public function getCartSummary(int $userId): array
    {
        try {
            $cartItems = $this->cartRepository->getUserActiveCart($userId);
            $total = $cartItems->sum('total');
            $count = $cartItems->count();

            return [
                'success' => true,
                'message' => 'Lấy thông tin giỏ hàng thành công!',
                'data' => [
                    'total' => $total,
                    'count' => $count
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
}
