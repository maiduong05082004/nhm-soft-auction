@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold">Giỏ hàng của bạn</h1>
            
                @if ($cartItems->count() > 0)
                    <button
                        class="flex items-center gap-2 text-red-600 border-red-600 hover:bg-red-50 bg-transparent border rounded-lg px-4 py-2"
                        onclick="clearCart()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Xóa tất cả
                    </button>
                @endif
        </div>

        @if ($cartItems->count() == 0)
            <div class="text-center py-12">
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">Giỏ hàng trống</h2>
                <a href="" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
                <div class="lg:col-span-2 space-y-4" id="cart-items-container">
                    @foreach ($cartItems as $cartItem)
                        <div class="card bg-white shadow-xl rounded-lg p-6" id="cart-item-{{ $cartItem->product_id }}">
                            <div class="flex items-center space-x-4">
                                <div class="relative h-20 w-20 rounded-lg overflow-hidden">
                                    @if ($cartItem->product && $cartItem->product->images && $cartItem->product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $cartItem->product->images->first()->image_url) }}"
                                            alt="{{ $cartItem->product->name ?? 'Sản phẩm' }}" class="object-cover w-full h-full">
                                    @else
                                        <img src="{{ asset('images/default-avatar.png') }}"
                                            alt="{{ $cartItem->product->name ?? 'Sản phẩm' }}" class="object-cover w-full h-full">
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $cartItem->product->name ?? 'Sản phẩm không tồn tại' }}</h3>
                                    <p class="text-green-700 font-bold">{{ number_format($cartItem->price ?? 0, 0, ',', '.') }} ₫
                                    </p>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button class="btn btn-neutral btn-outline w-6 h-[31px] quantity-btn"
                                        onclick="updateQuantity('{{ $cartItem->product_id }}', {{ $cartItem->quantity - 1 }})"
                                        {{ $cartItem->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                    <span class="w-12 text-center font-medium" id="quantity-{{ $cartItem->product_id }}">{{ $cartItem->quantity }}</span>
                                    <button class="btn btn-neutral btn-outline w-6 h-[31px] quantity-btn"
                                        onclick="updateQuantity('{{ $cartItem->product_id }}', {{ $cartItem->quantity + 1 }})">+</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg" id="item-total-{{ $cartItem->product_id }}">
                                        {{ number_format($cartItem->total ?? 0, 0, ',', '.') }} ₫</p>
                                    <button class="btn btn-ghost btn-sm text-red-600 hover:text-red-700 hover:bg-red-50 hover:border-none"
                                        onclick="removeItem('{{ $cartItem->product_id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="lg:col-span-1">
                    <div class="card card-border w-96 bg-base-100 card-xl shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Tóm tắt đơn hàng</h3>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span>Tạm tính (<span id="cart-count">{{ count($cartItems) }}</span> sản phẩm)</span>
                                    <span id="cart-subtotal">{{ number_format($total, 0, ',', '.') }} ₫</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Phí vận chuyển</span>
                                    <span class="text-green-600">Miến phí</span>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="flex justify-between text-lg font-bold mb-6">
                                <span>Tổng cộng</span>
                                <span class="" id="cart-total">{{ number_format($total + 30000, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="space-y-3">
                                <a href="{{ route('cart.checkout') }}"
                                    class="btn w-full bg-green-700 hover:bg-green-800 text-white">Tiến hành thanh toán</a>
                                <a href="" class="btn btn-outline w-full">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('pages.cart.script')
@endsection
