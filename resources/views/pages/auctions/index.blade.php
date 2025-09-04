@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
	<h1 class="text-2xl font-bold mb-6">Phiên trả giá đang diễn ra</h1>

	@if(empty($auctions) || count($auctions) === 0)
		<div class="alert alert-info">Hiện chưa có phiên trả giá nào.</div>
	@else
		<div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6">
			@foreach ($auctions as $auction)
				@php
					$product = $auction->product;
					$firstImage = $product?->images?->first();
					$currentPrice = optional($auction->bids)->max('bid_price') ?? $auction->start_price;
					$cardProduct = [
						'id' => $product->id ?? null,
						'slug' => $product->slug ?? null,
						'name' => $product->name ?? 'Sản phẩm',
						'firstImage' => $firstImage ? ['image_url' => $firstImage->image_url] : null,
						'type_sale' => \App\Enums\Product\ProductTypeSale::AUCTION->value,
						'price_display' => number_format((float) $currentPrice, 0, ',', '.') . ' ₫',
						'views' => $product->views ?? null,
						'created_at' => $product->created_at ?? null,
					];
				@endphp
				<x-product-card :product="$cardProduct" />
			@endforeach
		</div>
	@endif
</div>
@endsection


