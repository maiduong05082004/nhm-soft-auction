@section('')

    @extends('layouts.app')

@section('content')
    <div id="wishlist-config" data-get="{{ route('wishlist.get-items') }}" data-add="{{ route('wishlist.add') }}"
        data-remove="{{ route('wishlist.remove', ':id') }}" data-clear="{{ route('wishlist.clear') }}">
    </div>

    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-center sm:text-left">Sản phẩm yêu thích của bạn</h1>
        </div>

        <div id="loading-container" class="text-center py-8 sm:py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>

        <!-- Empty State -->
        <div id="empty-container" class="text-center py-8 sm:py-12" style="display: none;">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-600 mb-4">Danh sách yêu thích trống</h2>
            <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>

        <!-- Content -->
        <div id="content-container" class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 pt-4" style="display: none;">
            <div class="lg:col-span-2 space-y-3 sm:space-y-4" id="wishlist-items-container">
                
            </div>

            <!-- Mobile action buttons -->
            <div class="flex sm:flex-row justify-end items-stretch sm:items-center gap-2 sm:gap-3 mb-4"
                id="mobile-action-buttons" style="display: none;">
                <button
                    class="flex items-center justify-center gap-2 text-red-600 border-red-600 hover:bg-red-50 bg-transparent border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95"
                    onclick="clearWishlist()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 sm:size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Xóa tất cả</span>
                </button>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed top-4 right-4 z-50 hidden sm:block">
        <div class="bg-white border-l-4 text-gray-700 p-3 sm:p-4 rounded shadow-lg max-w-xs sm:max-w-sm transition-all duration-300 transform translate-x-full"
            id="toast-content">
            <div class="flex items-center">
                <div class="flex-shrink-0" id="toast-icon">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3">
                    <p id="toast-message" class="text-xs sm:text-sm font-medium"></p>
                </div>
            </div>
        </div>
    </div>

    <div id="mobile-toast" class="fixed bottom-4 left-4 right-4 z-50 hidden sm:hidden">
        <div class="bg-white border-l-4 text-gray-700 p-3 rounded shadow-lg transition-all duration-300 transform translate-y-full"
            id="mobile-toast-content">
            <div class="flex items-center">
                <div class="flex-shrink-0" id="mobile-toast-icon">
                    <svg class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-2">
                    <p id="mobile-toast-message" class="text-xs font-medium"></p>
                </div>
            </div>
        </div>
    </div>
@endsection
