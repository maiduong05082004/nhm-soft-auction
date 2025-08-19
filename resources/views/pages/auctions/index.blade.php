@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
	<h1 class="text-2xl font-bold mb-6">Phiên đấu giá đang diễn ra</h1>

	@if(empty($auctions) || count($auctions) === 0)
		<div class="alert alert-info">Hiện chưa có phiên đấu giá nào.</div>
	@else
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
			@foreach ($auctions as $auction)
				@php
					$product = $auction->product;
					$image = $product?->images?->first()?->image_url ?? null;
				@endphp
				<div class="card bg-base-100 shadow">
					<figure class="h-44 bg-white">
						@if($image)
							<img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name ?? 'Sản phẩm' }}" class="object-contain h-44" />
						@else
							<img src="{{ asset('images/default-avatar.png') }}" alt="No image" class="object-contain h-44" />
						@endif
					</figure>
					<div class="card-body">
						<h2 class="card-title text-base">{{ $product->name ?? 'Sản phẩm' }}</h2>
						<div class="text-sm text-[#6c6a69]">Bắt đầu: {{ optional($auction->start_time)->format('d/m/Y H:i') }}</div>
						<div class="text-sm text-[#6c6a69]">Kết thúc: {{ optional($auction->end_time)->format('d/m/Y H:i') }}</div>
						<div class="mt-2">
							<span class="badge badge-warning badge-outline">Đấu giá</span>
							@if($auction->bids && $auction->bids->count())
								<span class="badge badge-ghost">{{ $auction->bids->count() }} lượt đấu giá</span>
							@else
								<span class="badge badge-ghost">0 lượt đấu giá</span>
							@endif
						</div>
						<div class="card-actions justify-end mt-4">
							<a href="{{ route('products.show', $product->slug) }}" class="btn btn-neutral">Xem chi tiết</a>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif
</div>
@endsection


