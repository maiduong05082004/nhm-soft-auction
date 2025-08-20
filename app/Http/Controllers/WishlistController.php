<?php

namespace App\Http\Controllers;

use App\Services\Wishlist\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function list()
    {
        return view('pages.wishlist.list');
    }

    public function getItems(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $items = $this->wishlistService->getByUserId($userId);
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách wishlist'
            ], 500);
        }
    }

    public function add(Request $request): JsonResponse
    {
        try {
            $productId = $request->input('product_id');
            $userId = Auth::id();
            $this->wishlistService->createOne($userId, $productId);

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được thêm vào wishlist'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function remove(Request $request): JsonResponse
    {
        try {
            $productId = $request->input('product_id');
            $userId = Auth::id();
            $this->wishlistService->deleteByUserIdAndProductId($userId, $productId);

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xoá khỏi wishlist'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xoá sản phẩm khỏi wishlist'
            ], 500);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $this->wishlistService->clear($userId);

            return response()->json([
                'success' => true,
                'message' => 'Wishlist đã được làm trống'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->message
            ], 500);
        }
    }
}
