@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

{{-- SEO / Meta --}}
@section('title', $page->title . ' | ' . config('app.name', 'Takara-ooku') ?? ($page->title ?? 'Trang'))
@section('meta_description', $page->meta_description ?? Str::limit(strip_tags($page->content ?? ''), 150))
@section('meta_keywords', $page->meta_keywords ?? '')
@section('og_title', $page->meta_title ?? ($page->title ?? config('app.name')))
@section('og_description', $page->meta_description ?? Str::limit(strip_tags($page->content ?? ''), 150))
@section('og_image', $page->image ? asset('storage/' . $page->image) : asset('images/default-og.jpg'))
@section('schema_type', 'Article')
@section('schema_name', $page->title ?? config('app.name'))

@section('content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                        </path>
                    </svg>
                    Trang chủ
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-900 font-medium">{{ $page->title }}</span>
            </nav>
        </div>
    </div>

    {{-- Hero Section with Image Background --}}
    @if ($page->image)
        <div class="relative h-96 flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 overflow-hidden"
            style="background-image: url('{{ asset('storage/' . $page->image) }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
            <div class="relative z-10 text-center text-white max-w-4xl px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">{{ $page->title }}</h1>
                @if ($page->excerpt)
                    <p class="text-lg md:text-xl opacity-90 mb-6 drop-shadow">{{ $page->excerpt }}</p>
                @endif

                <div class="flex items-center justify-center space-x-4 text-sm">
                    @if ($page->published_at)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($page->published_at)->translatedFormat('d/m/Y') }}
                        </div>
                    @endif

                    @if ($page->status == \App\Enums\CommonConstant::INACTIVE)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Bản nháp
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Simple Hero without image --}}
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 py-16 text-center">
            <div class="text-white max-w-4xl mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $page->title }}</h1>
                @if ($page->excerpt)
                    <p class="text-lg md:text-xl opacity-90">{{ $page->excerpt }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto">
        {{-- Main Content --}}
        <div class="bg-gray-50 min-h-screen">
            <div class="container mx-auto px-4 py-12">
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
                    {{-- Main Content Area --}}
                    <div class="xl:col-span-3">
                        <div
                            class="bg-white bg-opacity-95 backdrop-blur-sm border border-white border-opacity-20 rounded-2xl p-8 mb-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl shadow-lg">
                            {{-- Content Meta Info --}}
                            <div class="flex flex-wrap items-center justify-between mb-6 pb-4 border-b border-gray-200">
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    @if ($page->published_at)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($page->published_at)->translatedFormat('d F Y') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Social Share --}}
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600 mr-2">Chia sẻ:</span>
                                    <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                        target="_blank"
                                        class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                        </svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($page->title) }}&url={{ urlencode(request()->url()) }}"
                                        target="_blank"
                                        class="w-8 h-8 rounded-full bg-blue-400 text-white flex items-center justify-center hover:bg-blue-500 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            {{-- Main Content with Tailwind Typography --}}
                            <article
                                class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-h2:text-2xl prose-h2:font-semibold prose-h2:mt-8 prose-h2:mb-4 prose-h2:border-l-4 prose-h2:border-indigo-500 prose-h2:pl-4 prose-h3:text-xl prose-h3:font-semibold prose-h3:mt-6 prose-h3:mb-3 prose-h3:text-gray-800 prose-p:text-gray-600 prose-p:leading-relaxed prose-p:mb-5 prose-img:rounded-lg prose-img:shadow-lg prose-img:my-6 prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:text-indigo-800 hover:prose-a:underline prose-strong:text-gray-900 prose-ul:text-gray-600 prose-ol:text-gray-600 prose-li:mb-1">
                                {!! $page->content !!}
                            </article>

                            {{-- Tags or Categories if available --}}
                            @if (isset($page->tags) && $page->tags)
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700">Tags:</span>
                                        @foreach (explode(',', $page->tags) as $tag)
                                            <span
                                                class="px-3 py-1 bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700 rounded-full text-sm border border-blue-200 hover:from-blue-100 hover:to-purple-100 transition-all duration-200">
                                                {{ trim($tag) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Related Products --}}
                        @if ($products && $products->isNotEmpty())
                            <div
                                class="bg-white bg-opacity-95 backdrop-blur-sm border border-white border-opacity-20 rounded-2xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl shadow-lg">
                                <div class="flex items-center mb-6">
                                    <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded mr-4"></div>
                                    <h2 class="text-2xl font-bold text-gray-900">Sản phẩm nổi bật</h2>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($products as $product)
                                        <x-product-card :product="$product" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <aside class="xl:col-span-1 space-y-6">

                        {{-- Latest News --}}
                        <div
                            class="bg-white rounded-xl shadow-lg border border-black border-opacity-5 p-6 transition-all duration-300 hover:shadow-xl">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                                        clip-rule="evenodd"></path>
                                    <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                                </svg>
                                Tin tức mới nhất
                            </h3>

                            @if ($news && $news->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($news as $featured)
                                        <li
                                            class="group hover:bg-gray-50 rounded-lg transition-all md:block hidden duration-200 relative">
                                            <a href="{{ route('news.detail', $featured->slug) }}"
                                                class="absolute inset-0 z-10" aria-hidden="true">
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
                                                    <button type="button"
                                                        class="bg-white p-1 rounded shadow-sm relative z-20">
                                                    </button>
                                                </div>
                                            </div>
                                        </li>



                                        <li
                                            class="group hover:bg-gray-50 rounded-lg transition-all duration-200 block md:hidden">
                                            <x-article-card :article="$featured" />
                                        </li>
                                    @endforeach
                                </div>

                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <a href="{{ route('news.list') }}"
                                        class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                        Xem tất cả tin tức
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">Chưa có tin tức nào.</p>
                                </div>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    {{-- Responsive Styles for Mobile --}}
    <style>
        @media (max-width: 768px) {
            .text-4xl.md\:text-5xl {
                font-size: 1.875rem !important;
            }
        }
    </style>
@endsection
