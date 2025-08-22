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
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="text-center">
                                    <a href="{{ route('products.list') }}"
                                        class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-shopping-bag
                                            class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-shopping-bag>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Sản phẩm
                                        </h3>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('products.list', ['product_type' => 'sale']) }}"
                                        class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-sparkles
                                            class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-sparkles>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Đang bán
                                        </h3>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('products.list', ['product_type' => 'auction']) }}"
                                        class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-ticket class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-ticket>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Đấu giá
                                        </h3>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('news.list') }}" class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-newspaper
                                            class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-newspaper>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Tin tức
                                        </h3>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('products.list') }}"
                                        class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-user class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-user>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Hội viên
                                        </h3>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <a href="" class="block hover:opacity-80 transition-opacity">
                                        <x-heroicon-o-shopping-bag
                                            class="w-8 h-8 mx-auto mb-2 rounded-lg"></x-heroicon-o-shopping-bag>
                                        <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                            Giới thiệu
                                        </h3>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4">
                            <h2 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
                                Tìm kiếm theo danh mục
                            </h2>

                            <div class="grid grid-cols-3 md:grid-cols-5 lg:hidden gap-3 mb-4">
                                @if (isset($categories))
                                    @foreach ($categories as $category)
                                        <div class="text-center">
                                            <a href="{{ route('products.list', ['category_id' => $category->id]) }}"
                                                class="block hover:opacity-80 transition-opacity">
                                                @if (isset($category['image']))
                                                    <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($category['image']) }}"
                                                        class="w-16 h-16 mx-auto mb-2 rounded-lg object-cover"
                                                        alt="{{ $category['name'] }}" loading="lazy" />
                                                @else
                                                    <img src="{{ asset('images/product_default.jpg') }}"
                                                        class="w-16 h-16 mx-auto mb-2 rounded-lg object-cover"
                                                        alt="{{ $category['name'] }}" loading="lazy" />
                                                @endif
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
                                                <a href="{{ route('products.list', ['category_id' => $category->id]) }}"
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
                    <x-section-home :section="['title' => 'Sản phẩm nổi bật', 'target' => route('products.list')]" :products="$products1" />
                    <x-section-home :section="[
                        'title' => 'Sản phẩm đang phổ biến',
                        'target' => route('products.list', ['orderBy' => 'view_desc']),
                    ]" :products="$products2" />
                    <x-section-home :section="['title' => 'Sản phẩm mới', 'target' => route('products.list')]" :products="$products3" />

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
                    <x-section-home :section="[
                        'title' => 'Sản phẩm đấu giá',
                        'target' => route('products.list', ['product_type' => 'auction']),
                    ]" :products="$products4" />
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
                @foreach ($articles->take(9) as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        @endif

        @if ($articles->count() > 9)
            <div class="w-full flex justify-center">
                <div class="my-6">
                    <a href="{{ route('news.list') }}"
                        class="bg-slate-600 text-white rounded-lg py-2 px-4 hover:bg-slate-700 transition-colors">
                        Xem thêm
                    </a>
                </div>
            </div>
        @endif
    </div>

@endsection
