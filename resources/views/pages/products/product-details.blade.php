@extends('layouts.app')

@section('content')
    @php
        $images = $product->images->pluck('image')->toArray();
        $mainImage = $images[0] ?? null;
    @endphp


    <div class="max-w-7xl mx-auto lg:px-4 py-6">
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a>Trang chủ</a></li>
                <li><a>Sản phẩm</a></li>
                <li>{{ $product->name }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-12 gap-6 rounded-lg p-4">
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded mb-4">
                        <img id="main-image"
                            src="{{ isset($product_images->first()->image_url) ? asset('storage/' . $product_images->first()->image_url) : 'https://via.placeholder.com/800x600?text=No+Image' }}"
                            class="w-full h-full object-contain" alt="{{ $product->name }}">
                    </div>

                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($product_images as $img)
                            <button type="button" class="rounded overflow-hidden bg-white hover:ring-2 hover:ring-blue-500"
                                onclick="document.getElementById('main-image').src='{{ asset('storage/' . $img->image_url) }}'">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-40 object-cover"
                                    alt="thumb">
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <div class="text-base font-bold text-gray-600 pt-4 mb-4">Sản phẩm này cũng phổ biến</div>
                <div class="swiper popularProductsSwiper">
                    <div class="swiper-wrapper">
                        @foreach ($product_category as $item)
                            <div class="swiper-slide">
                                <a href="{{ route('products.show', $item) }}" class="block">
                                    @php
                                        $thumb = optional($item->images->first())->image_url;
                                    @endphp
                                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                        <img class="w-full h-32 object-cover" 
                                             src="{{ $thumb ? asset('storage/' . $thumb) : asset('images/default-user-icon.png') }}" 
                                             alt="{{ $item->name }}" />
                                        <div class="p-3">
                                            <h3 class="line-clamp-2 text-sm font-medium text-gray-900 mb-1">{{ $item->name }}</h3>
                                            <p class="text-sm text-red-500 font-bold">
                                                {{ number_format($item->price, 0, ',', '.') }} ₫
                                                <div class="text-xs text-red-500">Mua ngay</div> 

                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next !text-gray-600 !w-8 !h-8 !bg-white !rounded-full !shadow-md"></div>
                    <div class="swiper-button-prev !text-gray-600 !w-8 !h-8 !bg-white !rounded-full !shadow-md"></div>
                    <div class="swiper-pagination !bottom-0 !relative !mt-4"></div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-5">
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h1 class="text-2xl font-bold">
                        @if ($product->min_bid_amount > 0 && $product->type_sale === ($typeSale['AUCTION'] ?? 2))
                            Bắt đầu từ {{ number_format($product->min_bid_amount, 0, ',', '.') }} ₫ - {{ $product->name }}
                        @else
                            {{ $product->name }}
                        @endif
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span
                            class="px-2 py-1 rounded text-xs font-medium
						{{ $product->type_sale === ($typeSale['AUCTION'] ?? 2) ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-600' }}">
                            {{ $product->type_sale === ($typeSale['AUCTION'] ?? 2) ? 'Đấu giá' : 'Bán hàng' }}
                        </span>
                        @if ($product->created_at && $product->created_at->diffInDays(now()) <= 7)
                        <div class="badge badge-ghost">new</div>
                    @endif
                        @if ($product->category)
                            <span class="badge whitespace-normal break-words leading-snug">{{ $product->category->name }}</span>
                        @endif
                        
                        <span class="text-sm text-gray-600">Lượt xem: {{ number_format($product->view ?? 0) }}</span>
                    </div>

                    <div>
                        <div class="text-sm text-gray-600 mb-1">Giá hiện tại</div>
                        @if (isset($auction) && $product->type_sale === ($typeSale['AUCTION'] ?? 2))
                            <div class="text-3xl font-bold text-green-600">
                                {{ number_format($currentPrice, 0, ',', '.') }} ₫
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                ({{ $totalBids }} lượt đấu giá)
                            </div>
                        @else
                            <div>
                                <span class="text-2xl font-bold text-red-600">
                                    {{ number_format($product->price, 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                        @php
                            $auctionResult = ['success' => isset($auctionData), 'data' => $auctionData];
                        @endphp
                        
                        <div class="text-sm text-[#6c6a69] space-y-1">
                        </div>
                    @endif
                    
                    @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                        <div class="text-sm text-[#6c6a69] space-y-1">
                            <p><span class="font-bold">Thời gian còn lại:</span></p>
                        </div>
                        <div class="grid auto-cols-max grid-flow-col gap-5 text-center justify-center">
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                              <span class="countdown font-mono text-5xl">
                                <span id="days" style="--value:99;" aria-live="polite" aria-label="99">99</span>
                              </span>
                              days
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                              <span class="countdown font-mono text-5xl">
                                <span id="hours" style="--value:24;" aria-live="polite" aria-label="24">24</span>
                              </span>
                              hours
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                              <span class="countdown font-mono text-5xl">
                                <span id="minutes" style="--value:59;" aria-live="polite" aria-label="59">59</span>
                              </span>
                              min
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                              <span class="countdown font-mono text-5xl">
                                <span id="seconds" style="--value:59;" aria-live="polite" aria-label="59"></span>
                              </span>
                              sec
                            </div>
                          </div>
                    @endif
                    <div class="flex" role="group" aria-labelledby="stock-label">
                        @if($product->type_sale !== ($typeSale['AUCTION'] ?? 2))
                            <span id="stock-label" class="flex text-sm font-bold text-[#6c6a69]">Số lượng còn:</span>
                            <div class="flex text-sm rounded bg-gray-100 text-gray-700">
                                &nbsp;{{ $product->stock ?? 0 }}
                            </div>
                        @endif
                    </div>

                    <div class="!mt-0">
                        @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                            @if($auctionData && $auctionData['auction']->status === 'active')
                                <div class="space-y-4">
                                    <form id="bid-form" class="space-y-3">
                                        @csrf
                                        <div class="items-center !mt-0">
                                            <label class="text-sm text-[#6c6a69] space-y-1 font-bold">Giá đấu của bạn (tối thiểu: {{ number_format($auctionData['min_next_bid'], 0, ',', '.') }} ₫)</label>
                                            <input type="number" 
                                                   id="bid-price" 
                                                   name="bid_price" 
                                                   min="{{ $auctionData['min_next_bid'] }}"
                                                   step="{{ $auctionData['auction']->step_price }}"
                                                   value="{{ $auctionData['min_next_bid'] }}"
                                                   class="input input-bordered w-full text-sm rounded-lg">
                                        </div>
                                        <button type="submit" 
                                                class="w-full btn btn-neutral text-white font-semibold rounded-lg">
                                            Đấu giá ngay
                                        </button>
                                    </form>
                                    
                                    @if ($product->max_bid_amount)
                                        <div>
                                            <button type="button" 
                                                    class="w-full btn btn-outline font-semibold rounded-lg">
                                                Mua ngay: {{ number_format($product->max_bid_amount, 0, ',', '.') }} ₫
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline increment-bid" data-increment="100000">+100.000</button>
                                            <button type="button" class="btn btn-sm btn-outline increment-bid" data-increment="200000">+200.000</button>
                                            <button type="button" class="btn btn-sm btn-outline increment-bid" data-increment="500000">+500.000</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button class="btn btn-outline !border-[#6c6a69] rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-[1.2em]"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                                Yêu thích
                                            </button>
                                            <button class="btn btn-outline !border-[#6c6a69] rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                                  </svg>
                                                Chia sẻ
                                            </button>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center text-sm text-[#6c6a69]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                          </svg>
                                        {{-- {{ number_format($followersCount ?? 0) }} người đang theo dõi --}}
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-gray-500 mb-2">
                                        @if($auctionData)
                                            Phiên đấu giá chưa bắt đầu hoặc đã kết thúc
                                            <br><small>Status: {{ $auctionData['auction']->status }}</small>
                                        @else
                                            Chưa có phiên đấu giá cho sản phẩm này
                                        @endif
                                    </div>
                                    <button class="btn btn-disabled w-full" disabled>
                                        Đấu giá ngay
                                    </button>
                                </div>
                            @endif
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
    @include('pages.products.script')
@endsection