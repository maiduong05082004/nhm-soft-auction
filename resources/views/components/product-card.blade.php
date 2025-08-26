<article class="product-card group bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
    @php
        $url = isset($product['slug']) ? route('products.show', [$product['slug']]) : '#';

        $imageUrl = asset('images/product_default.jpg');
        if (isset($product['firstImage']) && $product['firstImage'] && isset($product['firstImage']['image_url'])) {
            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['firstImage']['image_url']);
        } elseif (isset($product['image']) && $product['image']) {
            $imageUrl = \App\Utils\HelperFunc::generateURLFilePath($product['image']);
        }
    @endphp

    <div class="relative bg-gray-50">
        <a href="{{ $url }}" class="block aspect-square">
            <img src="{{ $imageUrl }}"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                alt="{{ $product['name'] ?? 'Sản phẩm' }}" loading="lazy"
                onerror="this.src='{{ asset('images/product_default.jpg') }}'">
        </a>

        @if (isset($product['is_hot']) && $product['is_hot'])
            <span class="absolute top-2 left-2 badge badge-error gap-1 text-[10px]">Hot</span>
        @endif

        @if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::AUCTION->value)
            <span class="absolute top-2 right-2 badge badge-warning gap-1 text-[10px]">Đấu giá</span>
        @elseif (!empty($product['created_at']) && \Carbon\Carbon::parse($product['created_at'])->gt(now()->subWeek()))
            <span class="absolute top-2 right-2 badge badge-accent gap-1 text-[10px]">Mới</span>
        @endif

        <button type="button" 
            class="wishlist-btn absolute bottom-2 right-2 btn btn-xs btn-circle bg-white text-red-500 hover:bg-red-50 shadow"
            aria-label="Thêm vào danh sách yêu thích" data-id="{{ $product['id'] }}">
            <x-heroicon-o-heart class="h-3.5 w-3.5" />
        </button>
    </div>

    <div class="p-3">
        <h3 class="font-semibold text-[14px] text-slate-900 mb-1 line-clamp-2 min-h-[38px]">
            <a href="{{ $url }}" class="hover:text-blue-600 transition-colors">{{ $product['name'] ?? 'Tên sản phẩm' }}</a>
        </h3>

        <div class="flex items-center justify-between mb-2">
            <div class="text-xs text-slate-500">
                @if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::AUCTION->value)
                    Hiện tại:
                @else
                    Giá:
                @endif
            </div>
            <div class="text-[12px] font-bold text-orange-600 whitespace-nowrap overflow-hidden text-ellipsis">
                {{ $product['price_display'] ?? '0 ₫' }}
            </div>
        </div>

        @if (!empty($product['views']))
            <div class="flex items-center text-xs text-slate-500 mb-2">
                <x-heroicon-o-eye class="h-3 w-3 mr-1 flex-shrink-0" />
                <span class="truncate">{{ number_format($product['views']) }} lượt xem</span>
            </div>
        @endif

        @if (isset($product['type_sale']) && $product['type_sale'] == \App\Enums\Product\ProductTypeSale::SALE->value)
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ $url }}" class="btn btn-sm btn-outline w-full col-span-2 text-[11px]">Xem chi tiết</a>
                <form action="{{ route('cart.add', ['product' => $product['id']]) }}" method="POST" class="add-cart-form col-span-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <button type="submit" class="btn btn-sm btn-neutral w-full text-[11px]" title="Thêm vào giỏ" aria-label="Thêm vào giỏ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4.01" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="w-full">
                <a href="{{ $url }}" class="btn btn-sm btn-outline w-full">Xem chi tiết</a>
            </div>
        @endif
    </div>
</article>
