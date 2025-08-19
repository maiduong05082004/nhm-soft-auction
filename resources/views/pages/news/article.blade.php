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
    <section class="site-banner overflow-hidden max-w-7xl mx-auto" aria-label="Promotional Banner">
        <img src="{{ asset('images/') }}" class="w-full h-auto object-cover" alt="AuctionsClone promotional banner"
            loading="lazy">
    </section>
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 my-6 md:px-0 px-4">
        <article class="lg:col-span-8 bg-base-100 p-6 rounded-lg shadow">
            <h1 class="text-4xl font-bold mb-4">{{ $article->title }}</h1>

            <div class="flex items-center text-sm text-gray-500 mb-6">
                <span>Tác giả:  {{ $article->author->name ?? 'Tác giả ẩn danh' }}</span>
                @if (isset($article->publish_time))
                    <span class="mx-2">•</span>
                    <span>{{ $article->publish_time}}</span>
                @endif
            </div>

            @if ($article->thumbnail_url)
                <figure class="mb-6">
                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-auto rounded-lg">
                    @if ($article->caption)
                        <figcaption class="text-sm text-gray-500 mt-2">
                            {{ $article->caption }}
                        </figcaption>
                    @endif
                </figure>
            @endif

            <div class="prose prose-lg max-w-none">
                {!! $article->content !!}
            </div>

            @if (isset($article->seo['meta_keywords']))
                <div class="mt-6">
                    <span class="font-semibold">Từ khóa: </span>
                </div>
            @endif

            {{-- @if ($relatedArticles->count())
                <div class="mt-10">
                    <h2 class="text-2xl font-bold mb-4">Bài viết liên quan</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        @foreach ($relatedArticles as $related)
                            <li>
                                <a href="{{ route('articles.show', $related) }}" class="hover:text-primary">
                                    {{ $related->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
        </article>
        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-base-100 p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Bài viết liên quan</h2>
                <ul class="space-y-3">
                    @foreach ($related_articles as $featured)
                        <li class="flex gap-3">
                            <img src="{{ $featured->thumbnail_url ?? 'https://via.placeholder.com/80x60' }}"
                                alt="{{ $featured->title }}" class="w-20 h-14 object-cover rounded">
                            <a href="" class="text-sm font-semibold hover:text-primary">
                                {{ Str::limit($featured->title, 60) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Quảng cáo hoặc widget khác --}}
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-500">Quảng cáo</p>
                <div class="bg-gray-300 h-40 flex items-center justify-center rounded">
                    Banner 300x250
                </div>
            </div>
        </aside>
    </div>
@endsection
