@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-center sm:text-left">Giỏ hàng của bạn</h1>

            @if ($cartItems->count() > 0)
                <div class="hidden sm:flex sm:flex-row justify-end items-stretch sm:items-center gap-2 sm:gap-3">
                    <button
                        class="btn btn-neutral flex items-center justify-center gap-2 border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95"
                        id="update-all-btn" style="display: none;" onclick="updateAllCartItems()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.01M19.67 9.35l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63M9.348 9.348l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63M9.348 9.348l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63" />
                        </svg>
                        <span class="hidden sm:inline">Cập nhật tất cả</span>
                        <span class="sm:hidden">Cập nhật</span>
                    </button>

                    <button
                        class="flex items-center justify-center gap-2 text-red-600 border-red-600 hover:bg-red-50 bg-transparent border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95"
                        onclick="clearCart()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 sm:size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        <span class="hidden sm:inline">Xóa tất cả</span>
                        <span class="sm:hidden">Xóa</span>
                    </button>
                </div>
            @endif
        </div>

        @if ($cartItems->count() == 0)
            <div class="text-center py-8 sm:py-12">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-600 mb-4">Giỏ hàng trống</h2>
                <a href="{{ route('home') }}" class="btn btn-neutral">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 pt-4">
                <div class="lg:col-span-2 space-y-3 sm:space-y-4" id="cart-items-container">
                    @foreach ($cartItems as $cartItem)
                        <div class="card bg-white shadow-xl rounded-lg p-3 sm:p-6 transition-all duration-200 hover:shadow-2xl"
                            id="cart-item-{{ $cartItem->product_id }}">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 py-3 sm:py-6">
                                <div class="flex items-center gap-3 sm:gap-4 flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <input
                                            id="select-item-{{ $cartItem->product_id }}"
                                            name="selected_items[]"
                                            value="{{ $cartItem->product_id }}"
                                            type="checkbox"
                                            checked="checked"
                                            class="select-item checkbox checkbox-neutral checked:!bg-black checked:hover:!bg-black checked:focus:!bg-black checked:!border-transparent"
                                            data-product-id="{{ $cartItem->product_id }}"
                                        />
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div
                                            class="relative h-16 w-16 sm:h-20 sm:w-20 rounded-lg overflow-hidden ring-2 ring-gray-100 hover:ring-blue-300 transition-all duration-200">
                                            @if ($cartItem->product && $cartItem->product->images && $cartItem->product->images->count() > 0)
                                                <img src="{{ isset($cartItem->product->images->first()->image_url) ? \App\Utils\HelperFunc::generateURLFilePath($cartItem->product->images->first()->image_url) : 'https://via.placeholder.com/800x600?text=No+Image' }}"
                                                    alt="{{ $cartItem->product->name ?? 'Sản phẩm' }}"
                                                    class="object-cover w-full h-full hover:scale-110 transition-transform duration-200">
                                            @else
                                                <img src="{{ asset('images/default-avatar.png') }}"
                                                    alt="{{ $cartItem->product->name ?? 'Sản phẩm' }}"
                                                    class="object-cover w-full h-full hover:scale-110 transition-transform duration-200">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-base sm:text-lg mb-2 line-clamp-2">
                                            {{ $cartItem->product->name ?? 'Sản phẩm không tồn tại' }}</h3>

                                        <div class="flex items-center justify-between">
                                            <p class="text-green-700 font-bold text-sm sm:text-base">
                                                {{ number_format($cartItem->price ?? 0, 0, ',', '.') }} ₫
                                            </p>

                                            <div class="flex items-center space-x-2 sm:hidden">
                                                <button
                                                    class="btn btn-neutral btn-outline w-6 h-6 sm:w-10 sm:h-10 quantity-btn text-sm transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                                    onclick="decreaseQuantity('{{ $cartItem->product_id }}')"
                                                    {{ $cartItem->quantity <= 1 ? 'disabled' : '' }}>-</button>

                                                <input type="number" id="quantity-input-{{ $cartItem->product_id }}"
                                                    value="{{ $cartItem->quantity }}" min="1"
                                                    max="{{ $cartItem->product->stock }}"
                                                    class="input input-neutral quantity-input w-16 h-6 sm:w-20 sm:h-10 text-center text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-200"
                                                    data-product-id="{{ $cartItem->product_id }}"
                                                    data-original-quantity="{{ $cartItem->quantity }}">

                                                <button
                                                    class="btn btn-neutral btn-outline w-6 h-6 sm:w-10 sm:h-10 quantity-btn text-sm transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                                    onclick="increaseQuantity('{{ $cartItem->product_id }}')">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden items-center space-x-2 sm:flex">
                                    <button
                                        class="btn btn-neutral btn-outline w-6 h-6 sm:w-10 sm:h-10 quantity-btn text-sm transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                        onclick="decreaseQuantity('{{ $cartItem->product_id }}')"
                                        {{ $cartItem->quantity <= 1 ? 'disabled' : '' }}>-</button>

                                    <input type="number" id="quantity-input-{{ $cartItem->product_id }}"
                                        value="{{ $cartItem->quantity }}" min="1"
                                        max="{{ $cartItem->product->stock }}"
                                        class="input input-neutral quantity-input w-16 h-6 sm:w-20 sm:h-10 text-center text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-200"
                                        data-product-id="{{ $cartItem->product_id }}"
                                        data-original-quantity="{{ $cartItem->quantity }}">

                                    <button
                                        class="btn btn-neutral btn-outline w-6 h-6 sm:w-10 sm:h-10 quantity-btn text-sm transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                        onclick="increaseQuantity('{{ $cartItem->product_id }}')">+</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg" id="item-total-{{ $cartItem->product_id }}">
                                        {{ number_format($cartItem->total ?? 0, 0, ',', '.') }} ₫</p>
                                    <button
                                        class="btn btn-ghost btn-sm text-red-600 hover:text-red-700 hover:bg-red-50 hover:border-none"
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
                @if ($cartItems->count() > 0)
                    <div class="flex sm:hidden sm:flex-row justify-end items-stretch sm:items-center gap-2 sm:gap-3 mb-4">
                        <button
                            class="btn btn-neutral hidden items-center justify-center gap-2 border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95"
                            id="mobile-update-all-btn" onclick="updateAllCartItems()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.01M19.67 9.35l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63M9.348 9.348l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63m2.38-2.61l1.023.63M9.348 9.348l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63m-2.38 2.61l-1.023-.63" />
                            </svg>
                            <span>Cập nhật tất cả</span>
                        </button>

                        <button
                            class="flex items-center justify-center gap-2 text-red-600 border-red-600 hover:bg-red-50 bg-transparent border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95"
                            onclick="clearCart()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Xóa tất cả</span>
                        </button>
                    </div>
                @endif
                <div class="lg:col-span-1 sm:justify-self-end lg:justify-self-center">
                    <div
                        class="card card-border w-full sm:w-96 bg-base-100 card-xl shadow-sm sticky top-4 lg:top-4 bottom-0 lg:bottom-auto z-10 lg:z-auto">
                        <div class="card-body p-4 sm:p-6">
                            <h3 class="card-title text-lg sm:text-xl">Tóm tắt đơn hàng</h3>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm sm:text-base">
                                    <span>Tạm tính (<span id="cart-count">{{ count($cartItems) }}</span> sản phẩm)</span>
                                    <span id="cart-subtotal">{{ number_format($total, 0, ',', '.') }} ₫</span>
                                </div>
                                <div class="flex justify-between text-sm sm:text-base">
                                    <span>Phí vận chuyển</span>
                                    <span class="text-green-600">Miễn phí</span>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="flex justify-between text-lg sm:text-xl font-bold mb-6">
                                <span>Tổng cộng</span>
                                <span id="cart-total">{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="space-y-3">
                                <button onclick="saveChangesBeforeCheckout()"
                                    class="btn w-full bg-green-700 hover:bg-green-800 text-white text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl">
                                    Tiến hành thanh toán
                                </button>
                                <a href="{{ route('home') }}"
                                    class="btn btn-outline w-full text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95">
                                    Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>



    <dialog id="save-changes-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Lưu thay đổi trước khi thanh toán?</h3>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-500 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <div>
                        <p class="text-gray-700 mb-2">Bạn đã thay đổi số lượng một số sản phẩm trong giỏ hàng.</p>
                        <p class="text-sm text-gray-600">Bạn có muốn lưu những thay đổi này trước khi tiến hành thanh toán không?</p>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Lưu ý</span>
                    </div>
                    <p class="text-sm text-blue-700">Nếu không lưu, số lượng sản phẩm sẽ trở về giá trị ban đầu.</p>
                </div>
            </div>

            <div class="modal-action">
                <button class="btn btn-outline" onclick="closeSaveChangesModal()">
                    Không lưu
                </button>
                <button class="btn btn-primary" onclick="confirmSaveChanges()">
                    Lưu và thanh toán
                </button>
            </div>
        </div>
    </dialog>

    @include('pages.cart.script')
@endsection
