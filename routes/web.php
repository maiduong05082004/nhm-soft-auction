<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là nơi đăng ký các route web cho ứng dụng của bạn.
| Các route này sẽ load qua RouteServiceProvider và nằm trong group "web".
|
*/

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/verify/{id}/{hash}', [\App\Http\Controllers\AuthController::class, 'verify'])->name('verify');

Route::get('/file/{file_path}', [FileController::class, 'loadfile'])
    ->where('file_path', '.*')
    ->name('loadfile');


Route::prefix('tin-tuc')->group(function () {
    Route::get('/', [App\Http\Controllers\NewsController::class, 'list'])->name('news.list');
    Route::get('{slug}', [\App\Http\Controllers\NewsController::class, 'article'])->name('news.detail');
});

Route::prefix('san-pham')->group(function () {
    Route::get('/', [ProductController::class, 'list'])->name('products.list');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('products.show');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('yeu-thich')->group(function () {
    Route::get('', [WishlistController::class, 'list'])->name('wishlist.list');
    Route::post('/them', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');

    Route::get('/api/items', [WishlistController::class, 'getItems'])->name('wishlist.get-items');
});



Route::middleware(['auth:sanctum', 'verified'])->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
    Route::post('/remove-item', [CartController::class, 'removeItem'])->name('cart.remove-item');
    Route::post('/clear-cart', [CartController::class, 'clearCart'])->name('cart.clear-cart');

    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('cart.process');

    Route::get('/payment/qr/{order}', [CartController::class, 'qrPayment'])->name('payment.qr');
    Route::post('/payment/confirm/{order}', [CartController::class, 'confirmPayment'])->name('payment.confirm');
    Route::get('/order/success/{order}', [CartController::class, 'orderSuccess'])->name('order.success');
    Route::post('/auction/pay-now', [CartController::class, 'auctionPayNow'])->name('auction.pay-now');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('auctions')->group(function () {
    Route::get('/', [AuctionController::class, 'getActiveAuctions'])->name('auctions.index');
    Route::get('/{productId}', [AuctionController::class, 'show'])->name('auctions.show');
    Route::post('/{productId}/bid', [AuctionController::class, 'bid'])->name('auctions.bid');
    Route::get('/{auctionId}/user-history', [AuctionController::class, 'getUserBidHistory'])->name('auctions.user-history');
});
