@extends('layouts.app')

@section('content')
    @php
        $images = $product->images->pluck('image')->toArray();
        $mainImage = $images[0] ?? null;
    @endphp


    <div class="max-w-6xl mx-auto lg:px-4 py-6">
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a>Trang chủ</a></li>
                <li><a>Sản phẩm</a></li>
                <li>{{ $product->name }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-12 gap-6 bg-white rounded-lg shadow p-4">
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded mb-4">
                        <img id="main-image"
                            src="{{ isset($product_images->first()->image_url) ? asset('storage/' . $product_images->first()->image_url) : 'https://via.placeholder.com/800x600?text=No+Image' }}"
                            class="w-full h-full object-contain" alt="{{ $product->name }}">
                    </div>

                    <div class="grid grid-cols-3 lg:grid-cols-6 gap-3">
                        @foreach ($product_images as $img)
                            <button type="button" class="rounded overflow-hidden bg-white hover:ring-2 hover:ring-blue-500"
                                onclick="document.getElementById('main-image').src='{{ asset('storage/' . $img->image_url) }}'">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-20 object-cover"
                                    alt="thumb">
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="text-base font-bold text-gray-600 pt-4">Sản phẩm này cũng phổ biến</div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
                    @foreach ($product_category as $item)
                        <a href="{{ route('products.show', $item) }}" class="block max-w-36">
                            @php
                                $thumb = optional($item->images->first())->image_url;
                            @endphp
                            <img class="w-36 h-36 object-cover rounded" src="{{ $thumb ? asset('storage/' . $thumb) : asset('images/default-user-icon.png') }}" alt="{{ $item->name }}" />
                            <div class="mt-2">
                                <h2 class="line-clamp-1 text-xs">{{ $item->name }}</h2>
                                <p class="text-sm text-red-500 font-bold"><span class="text-[10px] text-[#6c6a69]">Mua ngay</span> {{ number_format($item->price, 0, ',', '.') }} ₫</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-span-12 lg:col-span-5">
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h1 class="text-2xl font-bold">
                        @if ($product->min_bid_amount > 0 && $product->type_sale === 'auction')
                            Bắt đầu từ {{ number_format($product->min_bid_amount, 0, ',', '.') }} ₫ - {{ $product->name }}
                        @else
                            {{ $product->name }}
                        @endif
                    </h1>

                    <div class="flex items-center gap-3">
                        <span
                            class="px-2 py-1 rounded text-xs font-medium
						{{ $product->type_sale === 'auction' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-600' }}">
                            {{ $product->type_sale === 'auction' ? 'Đấu giá' : 'Bán hàng' }}
                        </span>

                        @if ($product->category)
                            <span class="text-sm text-gray-600">Danh mục: {{ $product->category->name }}</span>
                        @endif

                        <span class="text-sm text-gray-600">Lượt xem: {{ number_format($product->view ?? 0) }}</span>
                    </div>

                    <div class="text-2xl font-bold text-red-600">
                        @if (isset($auction) && $product->type_sale === 'auction')
                            {{ number_format($auction->step_price, 0, ',', '.') }} ₫
                        @else
                            <div>
                                <span class="text-sm text-[#6c6a69]">Hiện tại</span>
                                <span class="text-2xl font-bold text-red-600">
                                    {{ number_format($product->price, 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($product->type_sale === 'auction')
                        <div class="text-sm text-[#6c6a69] space-y-1">
                            <p><span class="font-bold">Mức đặt giá tối thiểu:</span> <strong
                                    class="text-gray-900">{{ number_format($product->min_bid_amount, 0, ',', '.') }}
                                    ₫</strong></p>
                            @if ($product->max_bid_amount)
                                <p><span class="font-bold">Mua ngay:</span> <strong
                                        class="text-gray-900">{{ number_format($product->max_bid_amount, 0, ',', '.') }}
                                        ₫</strong></p>
                            @endif
                            @if ($product->start_time && $product->end_time)
                                <p><span class="font-bold">Thời gian:</span> <span
                                        class="text-gray-900">{{ $product->start_time }} → {{ $product->end_time }}</span>
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700"><span class="font-bold text-[#6c6a69]">Số
                                lượng còn:</span></label>
                        <div class="inline-flex items-center px-3 py-1 rounded bg-gray-100 text-gray-700">
                            {{ $product->stock ?? 0 }}
                        </div>
                    </div>

                    <div class="pt-2">
                        @if ($product->type_sale === 'auction')
                            <a href="#"
                                class="w-full inline-flex items-center justify-center px-4 py-3 btn btn-neutral text-white font-semibold rounded-lg">
                                Đặt giá thầu
                            </a>
                        @else
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="flex items-center space-x-4 mb-4">
                                    <label class="font-medium">Số lượng:</label>
                                    <input type="number" name="quantity" value="1" min="1"
                                        class="input input-bordered w-20">
                                </div>
                                <button type="submit" class="btn btn-neutral w-full">
                                    Thêm vào giỏ hàng
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="flex gap-4 border-t border-slate-300 pt-4">
                        <div class="flex ">
                            <div class="">
                                @if ($user->avatar == null)
                                    <img src="{{ asset('/images/default-user-icon.png') }}" alt="{{ $user->name }}"
                                        class="rounded-full w-12 h-12 object-cover">
                                @else
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                        class="rounded-full w-12 h-12 object-cover">
                                @endif
                            </div>
                            <div class="ms-2">
                                <div class="font-semibold">{{ $user->name ?? '' }}</div>
                                <div class="flex">
                                    <div class="rating">
                                        <div class="mask mask-star-2 bg-[#ffda45]" aria-label="1 star"></div>
                                        <div class="mask mask-star-2 bg-[#ffda45]" aria-label="2 star"></div>
                                        <div class="mask mask-star-2 bg-[#ffda45]" aria-label="3 star" aria-current="true">
                                        </div>
                                        <div class="mask mask-star-2 bg-[#ffda45]" aria-label="4 star"></div>
                                        <div class="mask mask-star-2 bg-[#ffda45]" aria-label="5 star"></div>
                                    </div>
                                    <div class="ms-1 text-gray-400">
                                        (50 đánh giá)
                                    </div>
                                </div>

                            </div>
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
            <div class="col-span-12 bg-white p-4">
                <h3 class="font-semibold mb-2">Mô tả sản phẩm</h3>
                {!! $product->description ?? '' !!}
            </div>
        </div>
    </div>
@endsection
