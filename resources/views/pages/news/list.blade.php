@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-12">
            @if ($primary)
                <div class="overflow-hidden" aria-label="Promotional Banner">
                    <img src="{{ asset('images/banner_buyeeEnSp.png') }}" class="w-full max-h-[585px] object-cover"
                        alt="AuctionsClone promotional banner" loading="lazy">
                </div>
            @else
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 mt-14">
                        Tin Tức & Sự Kiện
                    </h1>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Cập nhật những thông tin mới nhất về công nghệ, kinh doanh và xã hội
                    </p>
                </div>
            @endif
            <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
                <form action="{{ route('news.list') }}" method="GET" class="flex flex-col md:flex-row gap-4">

                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="q" value="{{ request('search') }}"
                                placeholder="Tìm kiếm bài viết..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="md:w-48">
                        <select name="danh-muc"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($categories as $node)
                                @include('partial.category-node', ['node' => $node, 'depth' => 0])
                            @endforeach
                        </select>
                    </div>

                    <div class="md:w-48">
                        <select name="sap-xep"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Mọi bài viết</option>
                            <option value="view">Lượt xem</option>
                            <option value="sort">Độ ưu tiên</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="btn btn-neutral text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                </form>
            </div>

            @if (request('search') || request('category'))
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-blue-800 font-medium">Kết quả tìm kiếm:</span>
                            @if (request('search'))
                                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-search mr-1"></i>
                                    "{{ request('search') }}"
                                </span>
                            @endif
                            @if (request('category'))
                                <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $categories->where('slug', request('category'))->first()?->name ?? request('category') }}
                                </span>
                            @endif
                            <span class="text-gray-600">
                                ({{ $articles->total() }} bài viết)
                            </span>
                        </div>
                        <a href="{{ route('news.list') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-times mr-1"></i>Xóa bộ lọc
                        </a>
                    </div>
                </div>
            @endif

            <section class="my-12">
                @if ($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($articles as $article)
                            <x-article-card :article="$article" />
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $articles->links('pagination::daisyui') }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <i class="fas fa-search text-gray-400 text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Không tìm thấy bài viết nào
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Thử thay đổi từ khóa tìm kiếm hoặc danh mục để xem thêm kết quả.
                        </p>
                        <a href="{{ route('news.list') }}"
                            class="btn btn-neutral text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-list mr-2"></i>Xem tất cả bài viết
                        </a>
                    </div>
                @endif
            </section>

            <section class="mt-16 bg-gradient-to-r from-[#646068] to-[#777f92] rounded-2xl p-8 text-white text-center">
                <h3 class="text-2xl md:text-3xl font-bold mb-4">
                    Đăng ký nhận tin tức mới nhất
                </h3>
                <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
                    Nhận những bài viết mới nhất và thông tin độc quyền trước người khác
                </p>
                <div class="max-w-md mx-auto flex gap-4">
                    <input type="email" placeholder="Nhập email của bạn..."
                        class="flex-1 px-4 py-3 rounded-lg text-blue-600 focus:outline-none focus:ring-2 focus:ring-white">
                    <button
                        class="bg-white text-blue-600 hover:bg-slate-400 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Đăng ký
                    </button>
                </div>
            </section>
        </div>
    </div>
@endsection
