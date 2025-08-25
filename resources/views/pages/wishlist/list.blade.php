@section('')
    @extends('layouts.app')
@section('content')

    <div class="max-w-6xl my-4 mx-auto px-2 sm:px-4 py-4 sm:py-8 bg-white">
        <div class="flex max-w-5xl mx-auto justify-between">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Danh sách yêu thích</h2>
            <div class="w-auto" >
                <button class="flex items-center justify-center gap-2 text-red-600 border-red-600 hover:bg-red-50 bg-transparent border rounded-lg px-3 py-2 text-sm sm:text-base transition-all duration-200 hover:scale-105 active:scale-95" id="btn-clear-wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4 sm:size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 48.667 0 0 0-7.5 0" />
                </svg>
                <span class="hidden sm:inline">Xóa toàn bộ</span>
                <span class="sm:hidden">Xóa</span>
            </button>
            </div>
        </div>
        <div id="loading-container" class="text-center py-8 sm:py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>
        <div class="max-w-4xl grid md:grid-cols-3 lg:grid-cols-4 grid-cols-2 mx-auto gap-4" id="wishlist-items-container">
            
        </div>
        <div id="empty-container" class="text-center py-8 sm:py-12" style="display: none;">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-600 mb-4">Danh sách yêu thích trống</h2>
            <a href="{{ route('home') }}" class="btn btn-neutral">Tiếp tục mua sắm</a>
        </div>
    </div>
@endsection
