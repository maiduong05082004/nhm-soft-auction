<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
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

Route::get('/', function () {
    return view('pages.dashboard');

});

Route::get('/verify/{id}/{hash}', [\App\Http\Controllers\AuthController::class, 'verify'])->name('verify');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/process', [CartController::class, 'processCheckout'])->name('cart.process');

Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
Route::post('/cart/remove-item', [CartController::class, 'removeItem'])->name('cart.remove-item');
Route::post('/cart/clear-cart', [CartController::class, 'clearCart'])->name('cart.clear-cart');

Route::get('/payment/qr/{order}', [CartController::class, 'qrPayment'])->name('payment.qr');
Route::post('/payment/confirm/{order}', [CartController::class, 'confirmPayment'])->name('payment.confirm');
Route::get('/order/success/{order}', [CartController::class, 'orderSuccess'])->name('order.success');
