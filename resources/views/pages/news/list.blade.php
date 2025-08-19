@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-12">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 my-4">
                    Tin Tức & Sự Kiện
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Cập nhật những thông tin mới nhất về công nghệ, kinh doanh và xã hội
                </p>
            </div>

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
                            @if (isset($categories))
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" @selected(request('category') == $category->slug)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endif
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
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
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
                            <article
                                class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                                <div class="relative">
                                    @if ($article->image)
                                        <img src="{{ asset('storage/articles/' . $article->image) }}"
                                            alt="{{ $article->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                                            <i class="fas fa-newspaper text-white text-3xl"></i>
                                        </div>
                                    @endif

                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-{{ $article->category->color ?? 'blue' }}-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $article->category->name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <h4 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2">
                                        {{ $article->title }}
                                    </h4>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {!! $article->content !!}
                                    </p>
                                    <div class="flex items-center justify-between mt-4">
                                        @if ($article['author']['avatar'])
                                            <div class="text-sm text-gray-500 flex gap-1">
                                                <img src="{{ asset('storage/avatar') . '/' . $article['author']['avatar'] }}"
                                                    class="rounded-2xl w-4" alt="">
                                                <span class="whitespace-nowrap">{{ $article['author']['name'] }}</span>
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500 flex gap-1">
                                                <x-heroicon-o-user class="w-4"></x-heroicon-o-user>
                                                <span class="whitespace-nowrap">{{ $article['author']['name'] }}</span>
                                            </div>
                                        @endif

                                        <div class="text-sm text-gray-500 flex gap-1">
                                            <x-heroicon-o-eye class="w-4"></x-heroicon-o-eye>
                                            <span class="whitespace-nowrap">{{ $article['view'] }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-xs text-gray-500 flex">
                                            <div class="">
                                                {{ \Carbon\Carbon::parse($article->publish_time)->diffForHumans() }}</div>
                                        </span>
                                        <a href="{{ route('news.detail', $article->slug) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Đọc thêm <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
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
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-list mr-2"></i>Xem tất cả bài viết
                        </a>
                    </div>
                @endif
            </section>

            <section class="mt-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white text-center">
                <h3 class="text-2xl md:text-3xl font-bold mb-4">
                    Đăng ký nhận tin tức mới nhất
                </h3>
                <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
                    Nhận những bài viết mới nhất và thông tin độc quyền trước người khác
                </p>
                <div class="max-w-md mx-auto flex gap-4">
                    <input type="email" placeholder="Nhập email của bạn..."
                        class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
                    <button
                        class="bg-white text-blue-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Đăng ký
                    </button>
                </div>
            </section>
        </div>
    </div>
@endsection
