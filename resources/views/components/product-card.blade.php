<article
    class="product-card group relative rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg bg-[#f5f5f5]">
    @php
        $url = isset($product['slug']) ? route('products.show', [$product['slug']]) : '#';

        $imageUrl = asset('images/product_default.jpg');
        if (isset($product['firstImage']) && $product['firstImage'] && isset($product['firstImage']['image_url'])) {
            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['firstImage']['image_url']);
        } elseif (isset($product['image']) && $product['image']) {
            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['image']);
        }
    @endphp

    <div class="relative aspect-square overflow-hidden">
        @if (isset($product['is_hot']) && $product['is_hot'])
            <span
                class="absolute top-1 sm:top-2 left-1 sm:left-2 z-30 bg-red-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                Hot
            </span>
        @endif

        @if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::AUCTION->value)
            <span
                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-30 bg-orange-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded">
                <span class="hidden sm:inline">Đấu giá</span>
                <span class="sm:hidden">ĐG</span>
            </span>
        @elseif (!empty($product['created_at']) && \Carbon\Carbon::parse($product['created_at'])->gt(now()->subWeek()))
            <span
                class="absolute top-1 sm:top-2 right-1 sm:right-2 z-30 bg-green-500 text-white text-xs font-bold px-1 sm:px-2 py-1 rounded uppercase">
                Mới
            </span>
        @endif

        <a href="{{ $url }}" class="block w-full h-full z-0"
            aria-label="{{ $product['name'] ?? 'Xem sản phẩm' }}">
            <img src="{{ $imageUrl }}"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                onerror="this.src='{{ asset('images/product_default.jpg') }}'">
        </a>

        <button type="button" id="btn-add-wishlist"
            class="wishlist-btn absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1 sm:p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 z-40"
            aria-label="Thêm vào danh sách yêu thích" data-id="{{ $product['id'] }}">
            <x-heroicon-o-heart class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 hover:text-red-500" />
        </button>
    </div>

    <div class="p-2 sm:p-3">
        <h3
            class="text-xs sm:text-sm font-semibold text-gray-800 mb-2 group-hover:text-orange-500 transition-colors line-clamp-2 leading-tight min-h-[40px]">
            <a href="{{ $url }}" class="">
                {{ $product['name'] ?? 'Tên sản phẩm' }}
            </a>
        </h3>

        <div class="flex items-center justify-between mb-1 sm:mb-2">
            @if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::AUCTION->value)
                <span class="text-xs text-gray-500">Giá hiện tại:</span>
            @else
                <span class="text-xs text-gray-500">Giá:</span>
            @endif
            <span class="text-xs sm:text-sm font-bold text-orange-600">
                @php
                    $priceDisplay = '0đ';

                    if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::AUCTION->value) {
                        $auction = \App\Models\Auction::where('product_id', $product['id'] ?? null)->first();

                        if ($auction) {
                            $highestBid = $auction->bids()->orderBy('bid_price', 'desc')->first();
                            $currentPrice = $highestBid ? $highestBid->bid_price : $auction->start_price;
                            $priceDisplay = number_format($currentPrice, 0, ',', '.') . 'đ';
                        }
                    } else {
                        if (!empty($product['price'])) {
                            $priceDisplay = number_format($product['price'], 0, ',', '.') . 'đ';
                        } elseif (isset($product['min_bid_price']) && isset($product['max_bid_price'])) {
                            $priceDisplay = number_format($product['min_bid_price'], 0, ',', '.')
                                . ' - '
                                . number_format($product['max_bid_price'], 0, ',', '.')
                                . 'đ';
                        }
                    }
                @endphp
                {{ $priceDisplay }}
            </span>
        </div>

        @if (!empty($product['views']))
            <div class="flex items-center text-xs text-gray-500">
                <x-heroicon-o-eye class="h-3 w-3 mr-1 flex-shrink-0" />
                <span class="truncate">{{ number_format($product['views']) }} lượt xem</span>
            </div>
        @endif
    </div>
</article>
