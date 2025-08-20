@extends('layouts.app')

@section('title', 'Danh sách đấu giá - AuctionsClone')
@section('meta_description', 'Xem danh sách các sản phẩm đấu giá mới nhất.')
@section('meta_keywords', 'đấu giá, mua bán, auctions, sản phẩm')
@section('og_title', 'Danh sách đấu giá')
@section('og_description', 'Xem danh sách các sản phẩm đấu giá mới nhất.')
@section('og_image', asset('images/auctions-og.jpg'))
@section('schema_type', 'CollectionPage')
@section('schema_name', 'Danh sách đấu giá')


@section('content')

    <section class="site-banner overflow-hidden" aria-label="Promotional Banner">
        <img src="{{ asset('images/banner_buyeeEnSp.png') }}" class="w-full max-h-[585px] object-cover"
            alt="AuctionsClone promotional banner" loading="lazy">
    </section>

    <div id="page-home" class="my-6 max-w-7xl mx-auto px-4">
        <main class="site-main">
            <div class="grid lg:grid-cols-4 grid-cols-1 gap-6">
                <aside class="lg:col-span-1">
                    <div class="flex flex-col gap-6">

                        <div class="bg-white p-4">
                            <h2 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
                                Tìm kiếm theo danh mục
                            </h2>

                            <div class="grid grid-cols-3 md:grid-cols-5 lg:hidden gap-3 mb-4">
                                @if (isset($categories))
                                    @foreach ($categories as $category)
                                        <div class="text-center">
                                            <a href="{{ route("products.list",['category_id' => $category->id] )}}"
                                                class="block hover:opacity-80 transition-opacity">
                                                <img src="{{ asset('images/' . $category['image']) }}"
                                                    class="w-16 h-16 mx-auto mb-2 rounded-lg object-cover"
                                                    alt="{{ $category['name'] }}" loading="lazy">
                                                <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                                    {{ $category['name'] }}
                                                </h3>
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <h2>Không có danh mục nào</h2>
                                @endif
                            </div>

                            <nav class="hidden lg:block" aria-label="Category Navigation">
                                <ul class="space-y-2">
                                    @if (isset($categories))
                                        @foreach ($categories as $category)
                                            <li>
                                                <a href="{{ route("products.list",['category_id' => $category->id] )}}"
                                                    class="block p-2 font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded transition-colors">
                                                    {{ $category['name'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        <h2>Không có danh mục nào</h2>
                                    @endif
                                </ul>
                            </nav>
                        </div>

                        <div class="bg-white p-4">
                            <h2 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
                                Quảng cáo
                            </h2>
                            <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-3">
                                @php
                                    $ads = [
                                        [
                                            'image' => 'https://s.yimg.jp/images/ymstore/bnr/auc/benefit/376x376.png',
                                            'link' => '',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2022_11/1104_paypayguide/376_376.png',
                                            'link' => '',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/smartphone/softbank/v1/common/bnr/2021/0708/376_376.png',
                                            'link' => '',
                                        ],
                                        [
                                            'image' => 'https://s.yimg.jp/images/bank/campaign/202311/376_376.png',
                                            'link' => '',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/bb/promo/v2/external/bnr/221003/yafuoku_ybb.png',
                                            'link' => '',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2023_9/0911_copyright/376_376.png',
                                            'link' => '',
                                        ],
                                    ];
                                @endphp

                                @foreach ($ads as $index => $ad)
                                    <div class="aspect-square">
                                        <a href="{{ $ad['link'] }}"
                                            class="block hover:opacity-90 transition-opacity duration-300">
                                            <img src="{{ $ad['image'] }}" alt="Quảng cáo {{ $index + 1 }}"
                                                class="w-full h-full object-cover rounded-lg shadow-md hover:scale-105 transition-transform duration-300"
                                                loading="lazy">
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <section class="lg:col-span-3">
                    <div class="bg-white  shadow-sm  p-6 mb-6">
                        <header class="mb-6">
                            <h1 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
                                Sản phẩm mới
                            </h1>
                        </header>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach ($products1 as $product)
                                <article
                                    class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg ">
                                    <div class="relative aspect-square overflow-hidden">
                                        @if (isset($product['is_hot']) && $product['is_hot'])
                                            <span
                                                class="absolute top-1 sm:top-2 left-1 sm:left-2 z-10 bg-red-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                Hot
                                            </span>
                                        @elseif (!empty($product['created_at']) && \Carbon\Carbon::parse($product['created_at'])->gt(now()->subWeek()))
                                            <span
                                                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-green-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                Mới
                                            </span>
                                        @endif

                                        @if (isset($product['type']) && $product['type'] === 'auction')
                                            <span
                                                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-orange-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded">
                                                <span class="hidden sm:inline">Đấu giá</span>
                                                <span class="sm:hidden">ĐG</span>
                                            </span>
                                        @endif

                                        @php
                                            $imageUrl = asset('images/product_default.jpg');
                                            if (
                                                isset($product['firstImage']) &&
                                                $product['firstImage'] &&
                                                isset($product['firstImage']['image_url'])
                                            ) {
                                                $imageUrl = asset('storage/' . $product['firstImage']['image_url']);
                                            } elseif (isset($product['image']) && $product['image']) {
                                                $imageUrl = asset('storage/' . $product['image']);
                                            }
                                        @endphp

                                        <img src="{{ $imageUrl }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                                            onerror="this.src='{{ asset('images/product_default.jpg') }}'">
                                        <button type="button" id="btn-add-wishlist"
                                            class="wishlist-btn absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1 sm:p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                            aria-label="Thêm vào danh sách yêu thích" data-id="{{ $product['id'] }}">
                                            <x-heroicon-o-heart
                                                class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 hover:text-red-500" />
                                        </button>
                                    </div>

                                    <div class="p-2 sm:p-3">
                                        <h3
                                            class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors leading-tight min-h-[2.5rem] sm:min-h-[3rem] line-clamp-2">
                                            <a href="{{ isset($product['slug']) ? route('products.show', [$product['slug']]) : '' }}"
                                                class="hover:underline">
                                                {{ $product['name'] ?? 'Tên sản phẩm' }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                                            <span class="text-xs text-gray-500">Giá:</span>
                                            <span class="text-xs sm:text-sm font-bold text-orange-600">
                                                @php
                                                    $priceDisplay = '0đ';
                                                    if (isset($product['price']) && $product['price']) {
                                                        $priceDisplay = number_format($product['price']) . 'đ';
                                                    } elseif (
                                                        isset($product['min_bid_price']) &&
                                                        isset($product['max_bid_price'])
                                                    ) {
                                                        $priceDisplay =
                                                            number_format($product['min_bid_price']) .
                                                            ' - ' .
                                                            number_format($product['max_bid_price']) .
                                                            'đ';
                                                    }
                                                @endphp
                                                {{ $priceDisplay }}
                                            </span>
                                        </div>

                                        @if (isset($product['views']) && $product['views'])
                                            <div class="flex items-center text-xs text-gray-500">
                                                <x-heroicon-o-eye class="h-3 w-3 mr-1 flex-shrink-0" />
                                                <span class="truncate">{{ number_format($product['views']) }} lượt
                                                    xem</span>
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="w-full flex justify-center">
                            <div class="my-4">
                                <a href="{{ route('products.list') }}"
                                    class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                                    Xem thêm
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white p-6 mb-6">
                        <header class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
                                Sản phẩm nổi bật
                            </h2>
                        </header>

                        <div class="slide-product swiper">
                            <div class="swiper-wrapper mb-6">
                                @if ($products2->count())
                                    @foreach ($products2 as $product)
                                        <div class="swiper-slide">
                                            <article
                                                class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg ">
                                                <div class="relative aspect-square overflow-hidden">
                                                    @if (isset($product['is_hot']) && $product['is_hot'])
                                                        <span
                                                            class="absolute top-1 sm:top-2 left-1 sm:left-2 z-10 bg-red-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                            Hot
                                                        </span>
                                                    @elseif (!empty($product['created_at']) && \Carbon\Carbon::parse($product['created_at'])->gt(now()->subWeek()))
                                                        <span
                                                            class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-green-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                            Mới
                                                        </span>
                                                    @endif


                                                    @if (isset($product['type']) && $product['type'] === 'auction')
                                                        <span
                                                            class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-orange-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded">
                                                            <span class="hidden sm:inline">Đấu giá</span>
                                                            <span class="sm:hidden">ĐG</span>
                                                        </span>
                                                    @endif

                                                    @php
                                                        $imageUrl = asset('images/product_default.jpg');
                                                        if (
                                                            isset($product['firstImage']) &&
                                                            $product['firstImage'] &&
                                                            isset($product['firstImage']['image_url'])
                                                        ) {
                                                            $imageUrl = asset(
                                                                'storage/' . $product['firstImage']['image_url'],
                                                            );
                                                        } elseif (isset($product['image']) && $product['image']) {
                                                            $imageUrl = asset('storage/' . $product['image']);
                                                        }
                                                    @endphp

                                                    <img src="{{ $imageUrl }}"
                                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                                        alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                                                        onerror="this.src='{{ asset('images/product_default.jpg') }}'">

                                                    <button type="button"
                                                        class="heart-icon absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1 sm:p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                                        aria-label="Thêm vào danh sách yêu thích">
                                                        <x-heroicon-o-heart
                                                            class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 hover:text-red-500" />
                                                    </button>
                                                </div>

                                                <div class="p-2 sm:p-3">
                                                    <h3
                                                        class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors leading-tight min-h-[2.5rem] sm:min-h-[3rem] line-clamp-2">
                                                        <a href="{{ isset($product['slug']) ? route('products.show', [$product['slug']]) : '' }}"
                                                            class="hover:underline">
                                                            {{ $product['name'] ?? 'Tên sản phẩm' }}
                                                        </a>
                                                    </h3>

                                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                                        <span class="text-xs text-gray-500">Giá:</span>
                                                        <span class="text-xs sm:text-sm font-bold text-orange-600">
                                                            @php
                                                                $priceDisplay = '0đ';
                                                                if (isset($product['price']) && $product['price']) {
                                                                    $priceDisplay =
                                                                        number_format($product['price']) . 'đ';
                                                                } elseif (
                                                                    isset($product['min_bid_price']) &&
                                                                    isset($product['max_bid_price'])
                                                                ) {
                                                                    $priceDisplay =
                                                                        number_format($product['min_bid_price']) .
                                                                        ' - ' .
                                                                        number_format($product['max_bid_price']) .
                                                                        'đ';
                                                                }
                                                            @endphp
                                                            {{ $priceDisplay }}
                                                        </span>
                                                    </div>

                                                    @if (isset($product['views']) && $product['views'])
                                                        <div class="flex items-center text-xs text-gray-500">
                                                            <x-heroicon-o-eye class="h-3 w-3 mr-1 flex-shrink-0" />
                                                            <span class="truncate">{{ number_format($product['views']) }}
                                                                lượt
                                                                xem</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                @else
                                    <h2>Không có sản phẩm nào</h2>
                                @endif
                            </div>
                            <button
                                class="prev-btn p-2 bg-white rounded-full shadow hover:bg-[#b1b1b1] absolute left-2 top-1/3 transform -translate-y-1/4 z-10">
                                <x-heroicon-o-arrow-long-left class="h-6 w-6 mx-auto" />
                            </button>
                            <button
                                class="next-btn p-2 bg-white rounded-full shadow hover:bg-[#b1b1b1] absolute right-2 top-1/3 transform -translate-y-1/4 z-10">
                                <x-heroicon-o-arrow-long-right class="h-6 w-6 mx-auto" />
                            </button>
                        </div>
                        <div class="w-full flex justify-center">
                            <div class="my-4">
                                <a href="{{ route('products.list', array_merge(request()->query(), ['orderBy' => 'view_desc'])) }}"
                                    class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                                    Xem thêm
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 mb-6">
                        <header class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
                                Slide Banner
                            </h2>
                        </header>
                        <div class="slide-banner swiper">
                            <div class="swiper-wrapper mb-6">
                                @php
                                    $banners = [
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2025_4/0407_newbuyer/376_376-newbuyer_1000.png',
                                            'link' => '/',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2025_7/0724_lineoa/376_376-2.png',
                                            'link' => '/',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/paypayfleamarket/salespromo/2025/02/0225_LINEOA_kuji/img/bnr1_4_376_376.png',
                                            'link' => '/',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2024_3/0328_1start/376_376.png',
                                            'link' => '/',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2025_3/0324_reasonable/376_376.png',
                                            'link' => '/',
                                        ],
                                        [
                                            'image' =>
                                                'https://s.yimg.jp/images/auct/salespromotion/2025_8/0814_topics_charity/376_376.png',
                                            'link' => '/',
                                        ],
                                    ];
                                @endphp

                                @foreach ($banners as $banner)
                                    <div class="swiper-slide">
                                        <article>
                                            <a href="{{ $banner['link'] }}">
                                                <img src="{{ $banner['image'] }}" alt="Banner"
                                                    class="w-full h-auto object-cover shadow-md transition-transform duration-300 hover:scale-105">
                                            </a>
                                        </article>
                                    </div>
                                @endforeach
                            </div>

                            {{-- <div class="swiper-scrollbar-banner h-[4px]"></div> --}}

                            <button
                                class="prev-btn p-2 bg-white rounded-full shadow hover:bg-[#b1b1b1] absolute left-2 top-1/3 transform -translate-y-1/4 z-10">
                                <x-heroicon-o-arrow-long-left class="h-6 w-6 mx-auto" />
                            </button>
                            <button
                                class="next-btn p-2 bg-white rounded-full shadow hover:bg-[#b1b1b1] absolute right-2 top-1/3 transform -translate-y-1/4 z-10">
                                <x-heroicon-o-arrow-long-right class="h-6 w-6 mx-auto" />
                            </button>
                        </div>
                    </div>

                    <div class="bg-white  shadow-sm  p-6 mb-6">
                        <header class="mb-6">
                            <h1 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
                                Sản phẩm phổ biến
                            </h1>
                        </header>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach ($products3 as $product)
                                <article
                                    class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg ">
                                    <div class="relative aspect-square overflow-hidden">
                                        @if (isset($product['is_hot']) && $product['is_hot'])
                                            <span
                                                class="absolute top-1 sm:top-2 left-1 sm:left-2 z-10 bg-red-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                Hot
                                            </span>
                                        @elseif (!empty($product['created_at']) && \Carbon\Carbon::parse($product['created_at'])->gt(now()->subWeek()))
                                            <span
                                                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-green-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                                                Mới
                                            </span>
                                        @endif

                                        @if (isset($product['type']) && $product['type'] === 'auction')
                                            <span
                                                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-orange-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded">
                                                <span class="hidden sm:inline">Đấu giá</span>
                                                <span class="sm:hidden">ĐG</span>
                                            </span>
                                        @endif

                                        @php
                                            $imageUrl = asset('images/product_default.jpg');
                                            if (
                                                isset($product['firstImage']) &&
                                                $product['firstImage'] &&
                                                isset($product['firstImage']['image_url'])
                                            ) {
                                                $imageUrl = asset('storage/' . $product['firstImage']['image_url']);
                                            } elseif (isset($product['image']) && $product['image']) {
                                                $imageUrl = asset('storage/' . $product['image']);
                                            }
                                        @endphp

                                        <img src="{{ $imageUrl }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                                            onerror="this.src='{{ asset('images/product_default.jpg') }}'">

                                        <button type="button"
                                            class="heart-icon absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1 sm:p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                            aria-label="Thêm vào danh sách yêu thích">
                                            <x-heroicon-o-heart
                                                class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 hover:text-red-500" />
                                        </button>
                                    </div>

                                    <div class="p-2 sm:p-3">
                                        <h3
                                            class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors leading-tight min-h-[2.5rem] sm:min-h-[3rem] line-clamp-2">
                                            <a href="{{ isset($product['slug']) ? route('products.show', [$product['slug']]) : '' }}"
                                                class="hover:underline">
                                                {{ $product['name'] ?? 'Tên sản phẩm' }}
                                            </a>
                                        </h3>

                                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                                            <span class="text-xs text-gray-500">Giá:</span>
                                            <span class="text-xs sm:text-sm font-bold text-orange-600">
                                                @php
                                                    $priceDisplay = '0đ';
                                                    if (isset($product['price']) && $product['price']) {
                                                        $priceDisplay = number_format($product['price']) . 'đ';
                                                    } elseif (
                                                        isset($product['min_bid_price']) &&
                                                        isset($product['max_bid_price'])
                                                    ) {
                                                        $priceDisplay =
                                                            number_format($product['min_bid_price']) .
                                                            ' - ' .
                                                            number_format($product['max_bid_price']) .
                                                            'đ';
                                                    }
                                                @endphp
                                                {{ $priceDisplay }}
                                            </span>
                                        </div>

                                        @if (isset($product['views']) && $product['views'])
                                            <div class="flex items-center text-xs text-gray-500">
                                                <x-heroicon-o-eye class="h-3 w-3 mr-1 flex-shrink-0" />
                                                <span class="truncate">{{ number_format($product['views']) }} lượt
                                                    xem</span>
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="w-full flex justify-center">
                            <div class="my-4">
                                <a href="{{ route('products.list', array_merge(request()->query(), ['is_hot' => 'true'])) }}"
                                    class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                                    Xem thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <div class="my-6 max-w-7xl mx-auto px-4">

        <div class="text-center mb-6">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 my-4">
                Tin Tức & Sự Kiện
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Cập nhật những thông tin mới nhất về công nghệ, kinh doanh và xã hội
            </p>
        </div>
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
                            <h4 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2 leading-snug min-h-[3rem]">
                                {{ $article->title }}
                            </h4>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-snug min-h-[2.5rem]">
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
        @endif

        <div class="w-full flex justify-center">
            <div class="my-6">
                <a href="{{ route('news.list') }}"
                    class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                    Xem thêm
                </a>
            </div>
        </div>
    </div>

@endsection
