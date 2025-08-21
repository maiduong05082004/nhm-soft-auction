@section('')
    @extends('layouts.app')
@section('content')

    <div class="max-w-4xl mx-auto px-2 sm:px-4 py-4 sm:py-8">
        <div class="flex justify-between">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Danh sách yêu thích</h2>
            <div class="w-auto" >
                <button class="text-white bg-red-500 px-5 py-3 rounded-md" id="btn-clear-wishlist">Xóa toàn bộ</button>
            </div>
        </div>
        <div id="loading-container" class="text-center py-8 sm:py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>
        <div class="max-w-4xl mx-auto space-y-4" id="wishlist-items-container">
            <div
                class="group bg-white shadow-sm hover:shadow-lg rounded-xl border border-gray-100 hover:border-gray-200 transition-all duration-300 overflow-hidden">
            </div>
        </div>
        <div id="empty-container" class="text-center py-8 sm:py-12" style="display: none;">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-600 mb-4">Danh sách yêu thích trống</h2>
            <a href="{{ route('home') }}" class="btn btn-neutral">Tiếp tục mua sắm</a>
        </div>
    </div>
@endsection
