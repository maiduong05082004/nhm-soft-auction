@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Arr;

    $siteName = config('app.name', 'Takara Ooku');

    // Lấy dữ liệu SEO (array hoặc rỗng)
    $seo = is_array($article->seo) ? $article->seo : (array) ($article->seo ?? []);

    $title = $seo['title'] ?? ($article->meta_title ?? ($article->title ?? $siteName));
    $description =
        $seo['description'] ?? ($article->meta_description ?? Str::limit(strip_tags($article->content ?? ''), 160));

    // keywords luôn kiểm tra an toàn
    $keywords = $seo['keywords'] ?? ($article->meta_keywords ?? '');
    if (empty($keywords)) {
        $keywords = Str::slug($siteName, ' ');
    }

    // OG image
    $imagePath = $article->image ?? ($article->og_image ?? null);
    $ogImage = $imagePath ? \App\Utils\HelperFunc::generateURLFilePath($imagePath) : asset('images/auctions-og.jpg');

    // canonical
    $canonical = request()->url();

    // dates
    $publishedTime = optional(
        $article->publish_time ?? ($article->published_at ?? $article->created_at),
    )->toIso8601String();
    $modifiedTime = optional($article->updated_at ?? null)->toIso8601String();

    // author
    $authorName = $article->author?->name ?? ($article->author_name ?? 'Ban biên tập');
    $authorUrl = $article->author?->url ?? null;

    // publisher
    $publisherName = $siteName;
    $publisherLogo = file_exists(public_path('images/logo.png'))
        ? asset('images/logo.png')
        : asset('images/auctions-og.jpg');

    // robots
    $robots = !empty($seo['noindex']) ? 'noindex,follow' : 'index,follow';
    $schemaType = 'NewsArticle';
    // tags
    $section = $article->category?->name ?? ($article->category_name ?? null);
    $tagList = !empty($seo['keywords']) ? explode(',', $seo['keywords']) : [];
@endphp


{{-- Basic SEO sections used by partial.head --}}
@section('title', $title)
@section('meta_description', $description)
@section('meta_keywords', $keywords)
@section('og_title', $title)
@section('og_description', $description)
@section('og_image', $ogImage)
@section('schema_type', $schemaType)
@section('schema_name', $article->title ?? $siteName)

@push('head')
    <link rel="canonical" href="{{ $canonical }}" />
    <meta name="robots" content="{{ $robots }}" />

    {{-- Open Graph --}}
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="{{ $schemaType === 'NewsArticle' ? 'article' : 'article' }}" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    @if ($publishedTime)
        <meta property="article:published_time" content="{{ $publishedTime }}" />
    @endif
    @if ($modifiedTime)
        <meta property="article:modified_time" content="{{ $modifiedTime }}" />
    @endif
    @if ($authorName)
        <meta property="article:author" content="{{ $authorName }}" />
    @endif
    @if ($section)
        <meta property="article:section" content="{{ $section }}" />
    @endif
    @foreach ($tagList as $tag)
        <meta property="article:tag" content="{{ trim($tag) }}" />
    @endforeach

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
@endpush

@section('content')
    <section class="site-banner max-w-7xl mx-auto my-3" aria-label="Promotional Banner"></section>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 my-6 md:px-0 px-4">
        <article class="lg:col-span-8 bg-base-100 p-6 rounded-lg shadow" itemscope
            itemtype="https://schema.org/{{ $schemaType }}">
            <meta itemprop="mainEntityOfPage" content="{{ $canonical }}" />
            @if ($publishedTime)
                <meta itemprop="datePublished" content="{{ $publishedTime }}" />
            @endif
            @if ($modifiedTime)
                <meta itemprop="dateModified" content="{{ $modifiedTime }}" />
            @endif
            <meta itemprop="author" content="{{ $authorName }}" />
            <meta itemprop="publisher" content="{{ $publisherName }}" />

            <header class="prose max-w-none px-0 sm:px-6 lg:px-0 mb-6">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold leading-tight text-gray-900" itemprop="headline">
                    {{ $article->title }}
                </h1>

                @if (!empty($imagePath))
                    <figure class="mt-4">
                        <img src="{{ $ogImage }}" class="w-full h-auto object-cover rounded"
                            alt="{{ $article->title }} - {{ $siteName }}" loading="lazy" itemprop="image" onerror="this.src='{{ asset('images/product_default.jpg') }}'"/>
                    </figure>
                @endif

                <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:gap-4 text-sm text-gray-500">
                    <div class="flex items-center gap-2" itemprop="author" itemscope itemtype="https://schema.org/Person">
                        <x-heroicon-o-user class="h-4 w-4"></x-heroicon-o-user>
                        <span itemprop="name">{{ $authorName }}</span>
                    </div>

                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        @if ($publishedTime)
                            <time class="text-xs text-gray-500" datetime="{{ $publishedTime }}" itemprop="datePublished">
                                {{ Carbon::parse($publishedTime)->translatedFormat('d/m/Y H:i') }}
                            </time>
                        @endif
                        <span aria-hidden="true">•</span>
                        <x-heroicon-o-eye class="w-4 h-4"></x-heroicon-o-eye>
                        <span>{{ number_format($article->view ?? 0) }}</span>
                    </div>
                </div>
            </header>

            <article class="prose prose-lg lg:max-w-3xl mx-auto px-0 sm:px-6 min-h-[360px]" itemprop="articleBody">
                {!! $article->content !!}
            </article>

            <div class="bg-gray-50 p-4 rounded-lg mt-6">
                <h3 class="font-semibold text-gray-700 mb-3 text-center">Chia sẻ bài viết</h3>
                <div class="flex flex-wrap justify-center gap-3">
                    {{-- share buttons unchanged, keep as before --}}
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                        target="_blank"
                        class="share-btn btn btn-neutral text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <!-- svg + label -->
                        Facebook
                    </a>

                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                        target="_blank"
                        class="share-btn bg-blue-400 hover:bg-blue-500 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        Twitter
                    </a>

                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                        target="_blank"
                        class="share-btn bg-blue-700 hover:bg-blue-800 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        LinkedIn
                    </a>

                    <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                        target="_blank"
                        class="share-btn bg-blue-500 hover:bg-blue-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        Telegram
                    </a>

                    <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
                        target="_blank"
                        class="share-btn bg-green-500 hover:bg-green-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        WhatsApp
                    </a>

                    <button onclick="copyArticleLink()"
                        class="share-btn bg-gray-600 hover:bg-gray-700 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <span id="copy-text">Copy Link</span>
                    </button>

                    <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode('Tôi nghĩ bạn sẽ thích bài viết này: ' . $article->title . ' - ' . request()->url()) }}"
                        class="share-btn bg-gray-500 hover:bg-gray-600 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        Email
                    </a>

                    <button onclick="window.print()"
                        class="share-btn bg-gray-700 hover:bg-gray-800 text-white flex items-center px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        In bài
                    </button>
                </div>
            </div>
        </article>

        <aside class="lg:col-span-4 space-y-6">
            {{-- Related articles + Banner as before --}}
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
                                            onerror="this.src='{{ asset('images/product_default.jpg') }}'"
                                            loading="lazy">
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
                                            {{ Str::limit(strip_tags($featured->content), 80) }}
                                        </p>
                                    </div>
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

    {{-- JSON-LD: Article / NewsArticle + Breadcrumb + Publisher --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "{{ $schemaType }}",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $canonical }}"
      },
      "headline": {!! json_encode($article->title) !!},
      "description": {!! json_encode($description) !!},
      "image": {!! json_encode($ogImage) !!},
      @if($publishedTime)
      "datePublished": {!! json_encode($publishedTime) !!},
      @endif
      @if($modifiedTime)
      "dateModified": {!! json_encode($modifiedTime) !!},
      @endif
      "author": {
        "@type": "Person",
        "name": {!! json_encode($authorName) !!} @if($authorUrl), "url": {!! json_encode($authorUrl) !!} @endif
      },
      "publisher": {
        "@type": "Organization",
        "name": {!! json_encode($publisherName) !!},
        "logo": {
            "@type": "ImageObject",
            "url": {!! json_encode($publisherLogo) !!}
        }
      }@if(!empty($tagList)),
      "keywords": {!! json_encode(implode(', ', $tagList)) !!}@endif
    }
    </script>

    {{-- BreadcrumbList --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": {!! json_encode(url('/')) !!}
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Tin tức",
          "item": {!! json_encode(route('news.list')) !!}
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": {!! json_encode($article->title) !!},
          "item": {!! json_encode($canonical) !!}
        }
      ]
    }
    </script>

    <script>
        function copyArticleLink() {
            const text = window.location.href;
            navigator.clipboard?.writeText(text).then(() => {
                const el = document.getElementById('copy-text');
                if (el) el.textContent = 'Đã sao chép';
                setTimeout(() => el && (el.textContent = 'Copy Link'), 2000);
            }).catch(() => alert('Không thể sao chép liên kết'));
        }
    </script>
@endsection
