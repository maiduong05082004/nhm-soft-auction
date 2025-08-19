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
                'data' => $items->map(function ($item) {
                    return [
                        'id' => (string) $item->id,
                        'product_id' => (string) $item->product_id,
                        'product' => [
                            'id' => (string) $item->product_id ?? null,
                            'name' => (string) $item->product->name ?? 'Sản phẩm không tồn tại',
                            'image_url' => (string) $item->product && $item->product->images && $item->product->images->count() > 0
                                ? asset('storage/' . $item->product->images->first()->image_url)
                                : asset('images/default-avatar.png')
                        ]
                    ];
                })
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
            $this->wishlistService->insert($userId, $productId);

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được thêm vào wishlist'
            ]);
        } catch (\Exception $e) {

            dd($e);
            return response()->json([
                'success' => false,
                'message' => $e['message']
            ], 500);
        }
    }

    public function remove(Request $request, $productId): JsonResponse
    {
        try {
            $userId = Auth::id();
            $this->wishlistService->remove($userId, $productId);

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
                'message' => 'Lỗi khi làm trống wishlist'
            ], 500);
        }
    }
}
