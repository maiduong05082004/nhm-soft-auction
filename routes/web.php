<?php

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
