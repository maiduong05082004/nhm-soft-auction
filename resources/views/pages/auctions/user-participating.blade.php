@extends('layouts.app')

@section('title', 'Sản phẩm tôi đang trả giá')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Sản phẩm tôi đang trả giá</h1>
        <p class="text-gray-600">Danh sách các sản phẩm bạn đang tham gia trả giá</p>
    </div>

    @if($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($auctions as $auction)
                @php
                    $productData = [
                        'id' => $auction->product->id,
                        'name' => $auction->product->name,
                        'slug' => $auction->product->slug,
                        'type_sale' => \App\Enums\Product\ProductTypeSale::AUCTION->value,
                        'is_hot' => $auction->product->is_hot ?? false,
                        'created_at' => $auction->product->created_at,
                        'views' => $auction->product->views ?? 0,
                        'firstImage' => $auction->product->images && $auction->product->images->count() > 0 ? [
                            'image_url' => $auction->product->images->first()->image_url
                        ] : null,
                        'price' => (int) $auction->current_price_display,
                    ];
                @endphp
                
                <x-product-card :product="$productData" />
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Bạn chưa tham gia trả giá sản phẩm nào</h3>
            <p class="text-gray-600 mb-6">Hãy khám phá các sản phẩm đang trả giá và tham gia Trả giá ngay!</p>
            <a href="{{ route('auctions.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white btn btn-neutral">
                Xem tất cả sản phẩm trả giá
            </a>
        </div>
    @endif
</div>
@endsection
