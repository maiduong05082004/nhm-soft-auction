<?php

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
    return view('pages/dashboard', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/products', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
