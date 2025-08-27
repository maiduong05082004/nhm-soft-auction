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

@section('content')
    <section class="site-banner max-w-7xl mx-auto my-3" aria-label="Promotional Banner">

    </section>
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 my-6 md:px-0 px-4">
        <article class="lg:col-span-8 bg-base-100 p-6 rounded-lg shadow">
            <header class="prose max-w-none px-0 sm:px-6 lg:px-0 mb-6">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold leading-tight text-gray-900">
                    {{ $article->title }}
                </h1>
                @if (isset($article->image))
                    <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($article->image) }}"
                        class="w-full h-auto object-cover" alt="AuctionsClone promotional banner" loading="lazy">
                @else
                    <img src="{{ asset('storage/images/default.png') }} class="w-full h-auto object-cover"
                        alt="AuctionsClone promotional banner" loading="lazy">
                @endif
                <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:gap-4 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-user class="h-4 w-4"></x-heroicon-o-user>
                        <span>{{ $article->author?->name ?? 'Ban biên tập' }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        <span
                            class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($article->publish_time)->diffForHumans() }}</span>
                        <span aria-hidden="true">•</span>
                        <x-heroicon-o-eye class="w-4 h-4"></x-heroicon-o-eye>
                        <span>{{ $article->view }}</span>
                    </div>
                </div>
            </header>


            <article class="prose prose-lg lg:max-w-3xl mx-auto px-0 sm:px-6 min-h-[360px]">
                {!! $article->content !!}
            </article>


            @if (isset($article->seo['meta_keywords']))
                <div class="mt-6">
                    <span class="font-semibold">Từ khóa: </span>
                </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3 text-center">Chia sẻ bài viết</h3>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
                        class="share-btn btn btn-neutral text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Facebook
                    </a>

                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                        target="_blank"
                        class="share-btn bg-blue-400 hover:bg-blue-500 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                        Twitter
                    </a>

                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                        target="_blank"
                        class="share-btn bg-blue-700 hover:bg-blue-800 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                        LinkedIn
                    </a>

                    <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                        target="_blank"
                        class="share-btn bg-blue-500 hover:bg-blue-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
                        </svg>
                        Telegram
                    </a>

                    <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
                        target="_blank"
                        class="share-btn bg-green-500 hover:bg-green-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.108" />
                        </svg>
                        WhatsApp
                    </a>
                    <button onclick="copyArticleLink()"
                        class="share-btn bg-gray-600 hover:bg-gray-700 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span id="copy-text">Copy Link</span>
                    </button>

                    <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode('Tôi nghĩ bạn sẽ thích bài viết này: ' . $article->title . ' - ' . request()->url()) }}"
                        class="share-btn bg-gray-500 hover:bg-gray-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email
                    </a>

                    <button onclick="window.print()"
                        class="share-btn bg-gray-700 hover:bg-gray-800 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        In bài
                    </button>
                </div>
            </div>
        </article>
        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-base-100 p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Bài viết liên quan</h2>
                <ul class="space-y-3">
                    @foreach ($related_articles as $featured)
                        <li class="group hover:bg-gray-50 rounded-lg transition-all md:block hidden duration-200 relative">
                            <a href="{{ route('news.detail', $featured->slug) }}" class="absolute inset-0 z-10"
                                aria-hidden="true">
                                <span class="sr-only">Xem: {{ $featured->title }}</span>
                            </a>

                            <div class="flex gap-3 sm:gap-4 p-2 sm:p-3 relative z-0">
                                <div class="flex-shrink-0">
                                    <div class="relative overflow-hidden rounded-lg bg-gray-100">
                                        <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($featured->image) }}"
                                            alt="{{ $featured->title }}"
                                            class="w-16 h-12 sm:w-20 sm:h-14 md:w-24 md:h-16 object-cover transition-transform duration-300 group-hover:scale-105"
                                            onerror="this.src='{{ asset('images/product_default.jpg') }}'" loading="lazy">
                                        <div
                                            class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 flex flex-col justify-between">
                                    <div class="mb-1 sm:mb-2">
                                        <span
                                            class="block text-sm sm:text-base font-semibold text-gray-900 hover:text-primary transition-colors duration-200 leading-tight">
                                            <span class="line-clamp-2 sm:line-clamp-1">
                                                {{ Str::limit($featured->title, 60) }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex-1">
                                        <p
                                            class="text-gray-600 text-xs sm:text-sm line-clamp-2 sm:line-clamp-3 leading-relaxed">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($featured->content), 80) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2 z-20 flex items-center gap-2">
                                    <button type="button" class="bg-white p-1 rounded shadow-sm relative z-20">
                                    </button>
                                </div>
                            </div>
                        </li>



                        <li class="group hover:bg-gray-50 rounded-lg transition-all duration-200 block md:hidden">
                            <x-article-card :article="$featured" />
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($banner)
                <div class="bg-base-200 p-4 rounded-lg text-center">
                    <a href="{{ $banner['link_page'] }}" title="{{ $banner['name'] ?? 'Xem chi tiết quảng cáo' }}"
                        rel="nofollow sponsored">
                        <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($banner['url_image']) }}"
                            alt="{{ $banner['name'] ?? 'Quảng cáo banner' }}" loading="lazy" class="mx-auto rounded" />
                    </a>
                </div>
            @else
                <div class="bg-base-200 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">Quảng cáo</p>
                    <div class="bg-gray-300 h-40 flex items-center justify-center rounded">
                        <img src="/images/default-banner-300x250.png" alt="Banner quảng cáo 300x250" loading="lazy"
                            class="mx-auto rounded" />
                    </div>
                </div>
            @endif

        </aside>
    </div>
    <section class="max-w-7xl mx-auto px-4 lg:px-0 my-12">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Bài viết tương tự</h2>

            <div class="swiper similar-articles-swiper">
                <div class="swiper-wrapper">
                    @foreach ($related_articles as $similar)
                        <div class="swiper-slide">
                            <x-article-card :article="$similar" />
                        </div>
                    @endforeach
                </div>

                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>

                <div class="swiper-pagination mt-4"></div>
            </div>
        </div>
    </section>
@endsection
