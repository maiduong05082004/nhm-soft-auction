@extends('layouts.app')

@section('content')
@php
	$images = $product->images->pluck('image')->toArray();
	$mainImage = $images[0] ?? null;
@endphp

<div class="breadcrumbs text-sm">
    <ul>
      <li><a>Trang chủ</a></li>
      <li><a>Sản phẩm</a></li>
      <li>{{ $product->name }}</li>
    </ul>
  </div>
<div class="max-w-6xl mx-auto px-4 py-6">
	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12 lg:col-span-7">
			<div class="bg-white rounded-lg shadow p-4">
				<div class="aspect-[4/3] w-full overflow-hidden rounded mb-4">
					<img id="main-image" src="{{ $product_images->first()->image_url ? asset('storage/' . $product_images->first()->image_url) : 'https://via.placeholder.com/800x600?text=No+Image' }}" class="w-full h-full object-contain" alt="{{ $product->name }}">
				</div>

				<div class="grid grid-cols-6 gap-3">
					@foreach($product_images as $img)
						<button type="button"
							class="rounded overflow-hidden bg-white hover:ring-2 hover:ring-blue-500"
							onclick="document.getElementById('main-image').src='{{ asset('storage/' . $img->image_url) }}'">
							<img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-20 object-cover" alt="thumb">
						</button>
					@endforeach
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-5">
			<div class="bg-white rounded-lg shadow p-6 space-y-4">
				<h1 class="text-2xl font-bold">
                    @if($product->min_bid_amount > 0 && $product->type_sale === 'auction')
                    Bắt đầu từ {{ number_format($product->min_bid_amount, 0, ',', '.') }} ₫ - {{ $product->name }}
                    @else
                        {{ $product->name }}
                    @endif
                </h1>

				<div class="flex items-center gap-3">
					<span class="px-2 py-1 rounded text-xs font-medium
						{{ $product->type_sale === 'auction' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-600' }}">
						{{ $product->type_sale === 'auction' ? 'Đấu giá' : 'Bán hàng' }}
					</span>

					@if($product->category)
						<span class="text-sm text-gray-600">Danh mục: {{ $product->category->name }}</span>
					@endif

					<span class="text-sm text-gray-600">Lượt xem: {{ number_format($product->view ?? 0) }}</span>
				</div>

				<div class="text-3xl font-bold text-red-600">
					@if($product->type_sale === 'auction')
						{{ number_format($auction->step_price, 0, ',', '.') }} ₫
					@else
						{{ number_format($product->price, 0, ',', '.') }} ₫
					@endif
				</div>

				@if($product->type_sale === 'auction')
					<div class="text-sm text-gray-700 space-y-1">
						<p>Mức đặt giá tối thiểu: <strong>{{ number_format($product->min_bid_amount, 0, ',', '.') }} ₫</strong></p>
						@if($product->max_bid_amount)
							<p>Mức đặt giá tối đa: <strong>{{ number_format($product->max_bid_amount, 0, ',', '.') }} ₫</strong></p>
						@endif
						@if($product->start_time && $product->end_time)
							<p>Thời gian: {{ $product->start_time }} → {{ $product->end_time }}</p>
						@endif
					</div>
				@endif

				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Số lượng còn:</label>
					<div class="inline-flex items-center px-3 py-1 rounded bg-gray-100 text-gray-700">
						{{ $product->stock ?? 0 }}
					</div>
				</div>

				<div class="pt-2">
					@if($product->type_sale === 'auction')
						<a href="#"
						   class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
							Đặt giá thầu
						</a>
					@else
						<a href="#"
						   class="w-full inline-flex items-center justify-center px-4 py-3 hover:bg-slate-300 border-solid border border-slate-500 text-slate font-semibold rounded-lg">
							Thêm vào giỏ
						</a>
					@endif
				</div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
                    <div class="col-span-1">
                        <div class="mb-2">Người bán</div>
                    </div>
                    <div class="col-span-1">
                        {{ $user_sale->name ?? '' }}
                    </div>
                </div>
				<div class="pt-4 border-t border-slate-300">
					<h3 class="font-semibold mb-2">Thông tin sản phẩm</h3>
					<div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
						<div class="col-span-1">
							<div class="mb-2">Loại</div>
						</div>
						<div class="col-span-1">
							{{ $product->category->name ?? '' }}
						</div>
					</div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
						<div class="col-span-1">
							<div class="mb-2">Tình trạng sản phẩm</div>
						</div>
						<div class="col-span-1">
							{{-- {{ $product->cproduct_status ?? '' }} --}}
                            Chưa sử dụng
						</div>
					</div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
						<div class="col-span-1">
							<div class="mb-2">Số lượng</div>
						</div>
						<div class="col-span-1">
							{{ $product->stock ?? '' }}
						</div>
					</div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
						<div class="col-span-1">
							<div class="mb-2">Tình trạng sản phẩm</div>
						</div>
						<div class="col-span-1">
							{{-- {{ $product->cproduct_status ?? '' }} --}}
                            Chưa sử dụng
						</div>
					</div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-300 pt-4">
						<div class="col-span-1">
							<div class="mb-2">Phương thức thanh toán</div>
						</div>
						<div class="col-span-1">
							{{ $product->payment_method ?? 'Chưa có' }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="mt-6">
        <h3 class="font-semibold mb-2">Mô tả sản phẩm</h3>
        {!! $product->description ?? '' !!}
    </div>
</div>
@endsection