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
    <!-- Banner Section -->
    <section class="site-banner overflow-hidden" aria-label="Promotional Banner">
        <img src="{{ asset('images/banner_buyeeEnSp.png') }}" class="w-full h-auto object-cover"
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
                                            <a href="{{ $category['url'] }}"
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
                                                @if (isset($category['children']))
                                                    <details class="group">
                                                        <summary
                                                            class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                                            <span
                                                                class="font-medium text-gray-700">{{ $category['name'] }}</span>
                                                        </summary>
                                                        <ul class="mt-2 ml-4 space-y-1">
                                                            @foreach ($category['children'] as $child)
                                                                <li>
                                                                    <a href="{{ $child['url'] }}"
                                                                        class="block p-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded transition-colors">
                                                                        {{ $child['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </details>
                                                @else
                                                    <a href="{{ $category['url'] }}"
                                                        class="block p-2 font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded transition-colors">
                                                        {{ $category['name'] }}
                                                    </a>
                                                @endif
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
                                @for ($i = 1; $i <= 6; $i++)
                                    <div class="aspect-square">
                                        <a href="#" class="block hover:opacity-80 transition-opacity">
                                            <img src="{{ asset('images/376_376.png') }}"
                                                class="w-full h-full object-cover " alt="Quảng cáo {{ $i }}"
                                                loading="lazy">
                                        </a>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <section class="lg:col-span-3">

                    <div class="bg-white  shadow-sm  p-6 mb-6">
                        <header class="mb-6">
                            <h1 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200">
                                Sản phẩm đang phổ biến
                            </h1>
                        </header>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach ($products as $product)
                                <article
                                    class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">
                                    <div class="relative aspect-square overflow-hidden">
                                        <a href="{{ route('products.show', $product['slug']) }}">
                                            <img src="{{ $product['firstImage'] ? asset('storage/' . $product['firstImage']['image_url']) : asset('images/' . 'product_default.jpg') }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            alt="{{ $product['name'] }}" loading="lazy">
                                        </a>

                                        <button type="button"
                                            class="heart-icon absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                            aria-label="Thêm vào danh sách yêu thích">
                                            <x-heroicon-o-heart class="h-6 w-6 " />
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <h2
                                            class="text-xs font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            <a href="{{ route('products.show', $product['slug']) }}" class="hover:underline">
                                                {{ $product['name'] }}
                                            </a>
                                        </h2>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Giá:</span>
                                            <span class="text-xs font-bold text-orange-600">
                                                {{ $product['price'] ? number_format($product['price']) : number_format($product['min_bid_price']) . ' - ' . number_format($product['max_bid_price']) }}
                                                đ
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
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
                                @if ($products)
                                    @foreach ($products as $product)
                                        <div class="swiper-slide">
                                            <article
                                                class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">

                                                <div class="relative aspect-square overflow-hidden">
                                                    @if ($product['is_hot'])
                                                        <span
                                                            class="absolute top-2 left-2 z-10 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded uppercase">
                                                            Mới
                                                        </span>
                                                    @endif

                                                    <a href="{{ route('products.show', $product['slug']) }}">
                                                        <img src=" {{ $product['firstImage'] ? asset('storage/' . $product['firstImage']['image_url']) : asset('images/' . 'product_default.jpg') }}"
                                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                                            alt="{{ $product['name'] }}" loading="lazy">
                                                    </a>

                                                    <button type="button"
                                                        class="heart-icon absolute bottom-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                                        aria-label="Thêm vào danh sách yêu thích">
                                                        <x-heroicon-o-heart class="h-6 w-6 " />
                                                    </button>
                                                </div>

                                                <!-- Product Info -->
                                                <div class="p-3">
                                                    <h3 class="text-sm font-semibold text-blue-700 mb-2 line-clamp-2">
                                                        <a href="{{ route('products.show', $product['slug']) }}" class="hover:underline">
                                                            {{ $product['name'] }}
                                                        </a>
                                                    </h3>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs text-gray-500">Giá:</span>
                                                        <span class="text-sm font-bold text-gray-800">
                                                            {{ $product['price'] ? number_format($product['price']) : number_format($product['min_bid_price']) . ' - ' . number_format($product['max_bid_price']) }}
                                                            đ
                                                        </span>
                                                    </div>
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

                            {{-- <div class="swiper-scrollbar h-[2px]"></div> --}}
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
                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>

                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>

                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article>
                                        <a href="">
                                            <img src="{{ asset('images/') }}/376_376.png" alt="">
                                        </a>
                                    </article>
                                </div>
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
                                Sản phẩm đang phổ biến
                            </h1>
                        </header>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach ($products as $product)
                                <article
                                    class="product-card group relative bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">
                                    <div class="relative aspect-square overflow-hidden">
                                        <img src="{{ $product['firstImage'] ? asset('storage/' . $product['firstImage']['image_url']) : asset('images/' . 'product_default.jpg') }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            alt="{{ $product['name'] }}" loading="lazy">

                                        <button type="button"
                                            class="heart-icon absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                                            aria-label="Thêm vào danh sách yêu thích">
                                            <x-heroicon-o-heart class="h-6 w-6 " />
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <h2
                                            class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            <a href="" class="hover:underline">
                                                {{ $product['name'] }}
                                            </a>
                                        </h2>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Giá:</span>
                                            <span class="text-lg font-bold text-orange-600">
                                                {{ $product['price'] ? number_format($product['price']) : number_format($product['min_bid_price']) . ' - ' . number_format($product['max_bid_price']) }}
                                                đ
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

@endsection
