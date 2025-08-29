@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    $siteName = config('app.name', 'Takara Ooku');
    $pageNumber = $articles->currentPage() ?? 1;

    // Lấy input đúng tên: product_name, category_id, sort_by
    $searchTerm = request('q');
    $categoryId = request('category_id');
    $sortBy = request('sort_by');

    // Title logic
    if ($searchTerm) {
        $metaTitle = 'Kết quả tìm kiếm: "' . e($searchTerm) . '" - Tin tức & Sự kiện | ' . $siteName;
    } elseif ($categoryId) {
        $cat = $categories->where('id', $categoryId)->first();
        $catName = $cat?->name ?? $categoryId;
        $metaTitle = $catName . ' - Tin tức & Sự kiện | ' . $siteName;
    } else {
        $metaTitle = 'Tin tức & Sự kiện' . ($pageNumber > 1 ? " — Trang {$pageNumber}" : '') . ' | ' . $siteName;
    }

    // Meta description logic
    if ($searchTerm) {
        $metaDescription =
            'Kết quả tìm kiếm cho "' .
            e($searchTerm) .
            '" trên ' .
            $siteName .
            '. Tìm các bài viết, hướng dẫn, sự kiện và tin tức liên quan đến chủ đề bạn quan tâm.';
    } elseif ($categoryId) {
        $metaDescription =
            ($cat?->description ?? 'Các bài viết thuộc danh mục ' . ($catName ?? $categoryId) . ' trên ' . $siteName) .
            '. Cập nhật tin tức mới nhất, phân tích và hướng dẫn.';
    } else {
        $metaDescription =
            'Cập nhật tin tức & sự kiện mới nhất về công nghệ, kinh doanh và xã hội trên ' .
            $siteName .
            '. Bài viết chuyên sâu, thông tin chính xác và kịp thời.';
    }

    // Meta keywords (ngắn gọn)
    $metaKeywords = $categories->pluck('name')->take(10)->map(fn($n) => Str::slug($n, ' '))->join(', ');
    if (empty($metaKeywords)) {
        $metaKeywords = 'tin tức, sự kiện, ' . Str::slug($siteName, ' ');
    }

    // OG image: banner nếu có, fallback
    $ogImage = isset($primary) && $primary ? asset('images/banner_buyeeEnSp.png') : asset('images/auctions-og.jpg');

    // Canonical: base URL; include page param only if page > 1
    $currentUrl = request()->url();
    $canonical = $currentUrl . ($pageNumber > 1 ? '?page=' . $pageNumber : '');

    // Prev/Next for pagination
    $prevUrl = $articles->previousPageUrl();
    $nextUrl = $articles->nextPageUrl();

    // Robots: noindex when it's a search (product_name present), index otherwise
$robots = $searchTerm ? 'noindex,follow' : 'index,follow';

    // Prepare items for JSON-LD (limit to 10)
    $ldItems = $articles->take(10);
@endphp

{{-- Basic SEO sections used by partial.head --}}
@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)
@section('schema_type', 'CollectionPage')
@section('schema_name', 'Tin tức & Sự kiện — ' . $siteName)

{{-- Push additional head tags (requires @stack('head') present in head include) --}}
@push('head')
    <link rel="canonical" href="{{ $canonical }}" />
    @if ($prevUrl)
        <link rel="prev" href="{{ $prevUrl }}" />
    @endif
    @if ($nextUrl)
        <link rel="next" href="{{ $nextUrl }}" />
    @endif

    <meta name="robots" content="{{ $robots }}" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-12">
            @if ($primary)
                <div class="overflow-hidden" aria-label="Promotional Banner">
                    <img src="{{ asset('images/banner_buyeeEnSp.png') }}" class="w-full max-h-[585px] object-cover"
                        alt="{{ $siteName }} - Khuyến mãi" loading="lazy">
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
                {{-- Lưu ý: đổi name input trùng với controller: product_name, category_id, sort_by --}}
                <form action="{{ route('news.list') }}" method="GET" class="flex flex-col md:flex-row gap-4"
                    role="search" aria-label="Form tìm kiếm bài viết">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                                aria-hidden="true"></i>
                            <input type="text" name="q" value="{{ old('q', $searchTerm) }}"
                                placeholder="Tìm kiếm bài viết..." aria-label="Tìm kiếm bài viết"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="md:w-48">
                        <select name="category_id" aria-label="Lọc theo danh mục"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả danh mục</option>
                            {{-- Nếu partial.category-node xuất option theo id thì ok; nếu không, bạn có thể render trực tiếp --}}
                            @foreach ($categories as $node)
                                {{-- Giữ partial nếu nó trả option với value = id; nếu partial hiện tree bằng <option>, nó sẽ hoạt động --}}
                                @include('partial.category-node', [
                                    'node' => $node,
                                    'depth' => 0,
                                    'selected' => $categoryId ?? null,
                                ])
                            @endforeach
                        </select>
                    </div>

                    <div class="md:w-48">
                        <select name="sort_by" aria-label="Sắp xếp"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Mọi bài viết</option>
                            <option value="view" {{ $sortBy === 'view' ? 'selected' : '' }}>Lượt xem</option>
                            <option value="sort" {{ $sortBy === 'sort' ? 'selected' : '' }}>Độ ưu tiên</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="btn btn-neutral text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2" aria-hidden="true"></i>Tìm kiếm
                    </button>
                </form>
            </div>

            @if ($searchTerm || $categoryId)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg" role="status" aria-live="polite">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-blue-800 font-medium">Kết quả tìm kiếm:</span>
                            @if ($searchTerm)
                                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-search mr-1" aria-hidden="true"></i>
                                    "{{ $searchTerm }}"
                                </span>
                            @endif
                            @if ($categoryId)
                                <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                                    {{ $categories->where('id', $categoryId)->first()?->name ?? $categoryId }}
                                </span>
                            @endif
                            <span class="text-gray-600">
                                ({{ $articles->total() }} bài viết)
                            </span>
                        </div>
                        <a href="{{ route('news.list') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-times mr-1" aria-hidden="true"></i>Xóa bộ lọc
                        </a>
                    </div>
                </div>
            @endif

            <section class="my-12" aria-label="Danh sách bài viết">
                @if ($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($articles as $index => $article)
                            <x-article-card :article="$article" :position="$articles->firstItem() + $index" />
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{-- Giữ các query khác khi phân trang --}}
                        {{ $articles->appends(request()->except('page'))->links('pagination::daisyui') }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <i class="fas fa-search text-gray-400 text-6xl" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Không tìm thấy bài viết nào
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Thử thay đổi từ khóa tìm kiếm hoặc danh mục để xem thêm kết quả.
                        </p>
                        <a href="{{ route('news.list') }}"
                            class="btn btn-neutral text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-list mr-2" aria-hidden="true"></i>Xem tất cả bài viết
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
                        class="flex-1 px-4 py-3 rounded-lg text-blue-600 focus:outline-none focus:ring-2 focus:ring-white"
                        aria-label="Email nhận tin">
                    <button
                        class="bg-white text-blue-600 hover:bg-slate-400 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>Đăng ký
                    </button>
                </div>
            </section>
        </div>
    </div>

    {{-- JSON-LD for Collection + ItemList + Breadcrumb --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      "name": {!! json_encode(trim(strip_tags($metaTitle))) !!},
      "description": {!! json_encode(trim(strip_tags($metaDescription))) !!},
      "url": {!! json_encode($canonical) !!},
      "mainEntity": {
        "@type": "ItemList",
        "itemListElement": [
            @foreach($ldItems as $i => $a)
            {
              "@type": "ListItem",
              "position": {{ $i + 1 }},
              "url": {!! json_encode(route('news.detail', $a->slug ?? $a->id)) !!},
              "name": {!! json_encode($a->title ?? '') !!},
              @if(!empty($a->image))
              "image": {!! json_encode(asset('storage/' . $a->image)) !!},
              @endif
              "datePublished": {!! json_encode(optional($a->published_at ?? $a->created_at)->toIso8601String()) !!}
            }@if(!$loop->last),@endif
            @endforeach
        ]
      }
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
        }
      ]
    }
    </script>
@endsection
