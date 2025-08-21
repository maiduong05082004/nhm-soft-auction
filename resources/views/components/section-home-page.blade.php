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
                            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['firstImage']['image_url']);
                        } elseif (isset($product['image']) && $product['image']) {
                            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['image']);
                        }
                    @endphp

                    <img src="{{ $imageUrl }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                        onerror="this.src='{{ asset('images/product_default.jpg') }}'">
                    <button type="button" id="btn-add-wishlist"
                        class="wishlist-btn absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1 sm:p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                        aria-label="Thêm vào danh sách yêu thích" data-id="{{ $product['id'] }}">
                        <x-heroicon-o-heart class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 hover:text-red-500" />
                    </button>
                </div>

                <div class="p-2 sm:p-3">
                    <h3
                        class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors leading-tight">
                        <a href="{{ isset($product['slug']) ? route('products.show', [$product['slug']]) : '' }}"
                            class="hover:underline break-words whitespace-normal overflow-hidden"
                            style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; text-overflow: ellipsis; overflow: hidden;">
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
                                } elseif (isset($product['min_bid_price']) && isset($product['max_bid_price'])) {
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
