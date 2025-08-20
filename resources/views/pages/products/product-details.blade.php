@extends('layouts.app')

@section('content')
    @php
        $images = $product->images->pluck('image')->toArray();
        $mainImage = $images[0] ?? null;
    @endphp


    <div class="max-w-7xl mx-auto lg:px-4 py-6">
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a>Sản phẩm</a></li>
                <li>{{ $product->name }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-12 gap-6 rounded-lg p-4">
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded mb-4">
                        <img id="main-image"
                             src="{{ isset($product_images->first()->image_url) ? \App\Utils\HelperFunc::generateURLFilePath($product_images->first()->image_url) : 'https://via.placeholder.com/800x600?text=No+Image' }}"
                             class="w-full h-full object-contain" alt="{{ $product->name }}">
                    </div>
                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($product_images as $img)
                            <button type="button"
                                    class="rounded overflow-hidden bg-white hover:ring-2 hover:ring-blue-500"
                                    onclick="document.getElementById('main-image').src='{{ \App\Utils\HelperFunc::generateURLFilePath($img->image_url) }}'">
                                <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($img->image_url) }}" class="w-full h-40 object-cover"
                                     alt="thumb">
                            </button>
                        @endforeach
                    </div>
                </div>

                @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                        <div class="w-full">
                            <div class="grid w-full grid-cols-3">
                                <button onclick="showTab('description')"
                                        class="p-2 text-center border-b-2 border-blue-500 hover:text-slate-500">Mô
                                    tả</button>
                                <button onclick="showTab('shipping')"
                                        class="p-2 text-center border-b-2 border-transparent hover:text-slate-500">Vận
                                    chuyển</button>
                                <button onclick="showTab('seller')"
                                        class="p-2 text-center border-b-2 border-transparent hover:text-slate-500">Người
                                    bán</button>
                            </div>

                            <div id="description" class="tab-content mt-6">
                                <div class="max-w-none">
                                    <pre class="whitespace-pre-wrap font-sans text-base">{!! $product->description ?? 'Chưa có mô tả sản phẩm' !!}</pre>
                                </div>
                            </div>

                            <div id="shipping" class="tab-content mt-6" style="display: none;">
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span>Phí vận chuyển:</span>
                                        <span class="font-semibold">Miễn phí</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Thời gian giao hàng:</span>
                                        <span class="font-semibold">2-3 ngày làm việc</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Đóng gói:</span>
                                        <span class="font-semibold">Cẩn thận, an toàn</span>
                                    </div>
                                </div>
                            </div>

                            <div id="seller" class="tab-content mt-6" style="display: none;">
                                <div class="flex items-start space-x-4">
                                    <div class="h-16 w-16 bg-gray-300 rounded-full overflow-hidden">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                 class="object-cover w-full h-full">
                                        @else
                                            <img src="{{ asset('/images/default-user-icon.png') }}"
                                                 alt="{{ $user->name }}" class="object-cover w-full h-full">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg">{{ $user->name ?? 'Người bán' }}</h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div class="flex items-center">
                                                <span class="text-yellow-500">★</span>
                                                <span class="ml-1 font-semibold">4.5</span>
                                            </div>
                                            <span class="text-gray-500">(50 đánh giá)</span>
                                        </div>
                                        <p class="text-gray-600 mt-2">Cửa hàng uy tín chuyên bán các sản phẩm công nghệ
                                            chính
                                            hãng</p>
                                        <button
                                            class="mt-4 bg-transparent border border-gray-400 text-gray-700 py-2 px-4 rounded">
                                            <a href="https://www.nitori.com.vn/" target="_blank">
                                                Xem cửa hàng
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-span-12 lg:col-span-5 text-base">
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h1 class="text-2xl font-bold">
                        @if ($product->min_bid_amount > 0 && $product->type_sale === ($typeSale['AUCTION'] ?? 2))
                            Bắt đầu từ {{ number_format($product->min_bid_amount, 0, ',', '.') }} ₫ -
                            {{ $product->name }}
                        @else
                            {{ $product->name }}
                        @endif
                    </h1>
                    @if ($product->type_sale === ($typeSale['SALE'] ?? 1))
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-3">
                                <div class="rating w-20">
                                    <div class="mask mask-star-2 bg-[#ffda45]" aria-label="1 star"></div>
                                    <div class="mask mask-star-2 bg-[#ffda45]" aria-label="2 star"></div>
                                    <div class="mask mask-star-2 bg-[#ffda45]" aria-label="3 star" aria-current="true">
                                    </div>
                                    <div class="mask mask-star-2 bg-[#ffda45]" aria-label="4 star"></div>
                                    <div class="mask mask-star-2 bg-[#ffda45]" aria-label="5 star"></div>
                                </div>
                                <span class="ml-2 text-slate-500"><span class="font-bold text-black">3.1</span> (50 đánh
                                        giá)</span>
                                <div class="ml-5 text-slate-600">Đã bán: 118</div>
                            </div>
                        </div>
                    @endif
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
                            <span
                                class="badge whitespace-normal break-words leading-snug">{{ $product->category->name }}</span>
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
                                        <span id="days" style="--value:99;" aria-live="polite"
                                              aria-label="99">99</span>
                                    </span>
                                days
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                                    <span class="countdown font-mono text-5xl">
                                        <span id="hours" style="--value:24;" aria-live="polite"
                                              aria-label="24">24</span>
                                    </span>
                                hours
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                                    <span class="countdown font-mono text-5xl">
                                        <span id="minutes" style="--value:59;" aria-live="polite"
                                              aria-label="59">59</span>
                                    </span>
                                min
                            </div>
                            <div class="bg-neutral rounded-box text-neutral-content flex flex-col p-2">
                                    <span class="countdown font-mono text-5xl">
                                        <span id="seconds" style="--value:59;" aria-live="polite"
                                              aria-label="59"></span>
                                    </span>
                                sec
                            </div>
                        </div>
                    @endif


                    <div>
                        @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                            @if ($auctionData && $auctionData['auction']->status === 'active')
                                <div class="space-y-4">
                                    <form id="bid-form" action="{{ route('auctions.bid', $product->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="items-center !mt-0">
                                            <label class="text-sm text-[#6c6a69] space-y-1 font-bold">Giá đấu của bạn
                                                (tối thiểu: {{ number_format($auctionData['min_next_bid'], 0, ',', '.') }} ₫)</label>
                                            <input type="number" id="bid-price" name="bid_price"
                                                   min="{{ $auctionData['min_next_bid'] }}"
                                                   step="{{ $auctionData['auction']->step_price }}"
                                                   value="{{ $auctionData['min_next_bid'] }}"
                                                   class="input input-bordered w-full text-sm rounded-lg">
                                        </div>
                                        <button type="button"
                                                class="w-full btn btn-neutral text-white font-semibold rounded-lg"
                                                onclick="showBidConfirmation(event)">
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
                                            <button type="button" class="btn btn-sm btn-outline increment-bid"
                                                    data-increment="100000">+100.000</button>
                                            <button type="button" class="btn btn-sm btn-outline increment-bid"
                                                    data-increment="200000">+200.000</button>
                                            <button type="button" class="btn btn-sm btn-outline increment-bid"
                                                    data-increment="500000">+500.000</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" class="btn btn-outline !border-[#6c6a69] rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                                     class="size-[1.2em]">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>
                                                Yêu thích
                                            </button>
                                            <button type="button" class="btn btn-outline !border-[#6c6a69] rounded-lg" onclick="shareProduct()">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                     class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                                </svg>
                                                Chia sẻ
                                            </button>
                                        </div>
                                    @endif

                                    <div class="flex items-center text-sm text-[#6c6a69]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                        </svg>
                                        {{ number_format($followersCount ?? 0) }} người đang theo dõi
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-gray-500 mb-2">
                                        @if ($auctionData)
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
                                <div class="flex items-center space-x-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#6c6a69] mb-2">Số lượng:</span>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button type="button" onclick="decreaseQuantity()"
                                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-l-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input type="number" id="quantity" name="quantity" value="1"
                                                       min="1" max="{{ $product->stock ?? 1 }}"
                                                       class="w-16 text-center border-0 focus:ring-0 focus:outline-none text-gray-700 !appearance-none">
                                                <button type="button" onclick="increaseQuantity()"
                                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-r-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-slate-600">
                                                Còn lại: {{ $product->stock ?? 0 }} sản phẩm
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-neutral w-full mt-4">
                                    Thêm vào giỏ hàng
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
                    <div class="card w-full bg-base-100 card-md shadow-sm mt-4">
                        <div class="card-body">
                            <h2 class="card-title">Lịch sử đấu giá</h2>
                            @if (!empty($auctionData['recent_bids']) && count($auctionData['recent_bids']) > 0)
                                <div class="space-y-3">
                                    @foreach ($auctionData['recent_bids'] as $bid)
                                        <div
                                            class="flex items-center justify-between py-2 border-b last:border-b-0 border-slate-300 text-base">
                                            <div>
                                                <div class="font-medium">
                                                    {{ \App\Utils\HelperFunc::maskMiddle($bid->user->name ?? 'Người dùng', 4, 3) }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ ($bid->bid_time ? \Carbon\Carbon::parse($bid->bid_time) : $bid->created_at ?? now())->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="font-semibold text-green-600">
                                                {{ number_format($bid->bid_price, 0, ',', '.') }} ₫</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-500">Chưa có lịch sử đấu giá</div>
                            @endif
                        </div>
                    </div>
                    <div role="alert" class="alert mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             class="stroke-info h-6 w-6 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Chỉ đấu giá khi bạn thực sự muốn mua sản phẩm. Việc đấu giá thành công có tính ràng buộc
                                pháp
                                lý.</span>
                    </div>
                @endif

                @if ($product->type_sale === ($typeSale['SALE'] ?? 1))
                    <div class="card w-full bg-base-100 card-md shadow-sm mt-4">
                        <div class="card-body">
                            <div class="mt-6">
                                <div class="flex items-start space-x-4">
                                    <div class="h-16 w-16 bg-gray-300 rounded-full overflow-hidden">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                 class="object-cover w-full h-full">
                                        @else
                                            <img src="{{ asset('/images/default-user-icon.png') }}"
                                                 alt="{{ $user->name }}" class="object-cover w-full h-full">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4">
                                            <h3 class="font-semibold text-lg">{{ $user->name ?? 'Người bán' }}</h3>
                                            @if (
                                                ($user->email_verified_at && $user->phone_verified_at) ||
                                                    ($user->email_verified_at && !$user->phone) ||
                                                    ($user->phone_verified_at && !$user->email))
                                                <div class="badge badge-primary">Verified</div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div class="flex items-center">
                                                <span class="text-yellow-500">★</span>
                                                <span class="ml-1 font-semibold">4.5</span>
                                            </div>
                                            <span class="text-gray-500">(50 đánh giá)</span>
                                        </div>
                                        <p class="text-gray-600 mt-2">Cửa hàng uy tín chuyên bán các sản phẩm công nghệ
                                            chính
                                            hãng</p>
                                        <button
                                            class="mt-4 bg-transparent border border-gray-400 text-gray-700 py-2 px-4 rounded">
                                            <a href="https://www.nitori.com.vn/" target="_blank">
                                                Xem cửa hàng
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($product->type_sale === ($typeSale['SALE'] ?? 1))
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <div class="w-full">
                    <div class="grid w-full grid-cols-3">
                        <button onclick="showTab('description')"
                                class="p-2 text-center border-b-2 border-blue-500 hover:text-slate-500">Mô tả</button>
                        <button onclick="showTab('shipping')"
                                class="p-2 text-center border-b-2 border-transparent hover:text-slate-500">Thông
                            tin</button>
                        <button onclick="showTab('reviews')"
                                class="p-2 text-center border-b-2 border-transparent hover:text-slate-500">Bình
                            luận</button>
                    </div>

                    <div id="description" class="tab-content mt-6">
                        <div class="max-w-none">
                            <pre class="whitespace-pre-wrap font-sans text-base">{!! $product->description ?? 'Chưa có mô tả sản phẩm' !!}</pre>
                        </div>
                    </div>

                    <div id="shipping" class="tab-content mt-6" style="display: none;">
                        <div class="space-y-4">
                            <h3 class="font-semibold mb-4 text-lg">Thông tin chi tiết sản phẩm</h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Loại:</span>
                                        <span
                                            class="text-slate-900">{{ $product->category->name ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Thương hiệu:</span>
                                        <span
                                            class="text-slate-900">{{ $product->brand ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Tình trạng sản phẩm:</span>
                                        <span class="text-slate-900">{{ $productStateLabel }}</span>
                                    </div>
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Số lượng:</span>
                                        <span
                                            class="text-slate-900">{{ $product->stock ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Người bán:</span>
                                        <span class="text-slate-900">{{ $user->name ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Ngày vận chuyển:</span>
                                        <span
                                            class="text-slate-900">{{ $product->dateShipping ??
                                                    'Giao hàng trong vòng 1-2 ngày sau khi thanh toán' }}</span>
                                    </div>
                                    <div class="flex justify-between py-6 border-b border-slate-300">
                                        <span class="font-medium text-slate-700">Phương thức thanh toán:</span>
                                        <span
                                            class="text-slate-900">{{ $productPaymentMethodLabel ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="reviews" class="tab-content mt-6" style="display: none;">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-1">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-slate-900 mb-2">{{ $averageRating }}</div>
                                    <div class="rating rating-lg mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $averageRating)
                                                <div class="mask mask-star bg-orange-400"
                                                     aria-label="{{ $i }} star"></div>
                                            @else
                                                <div class="mask mask-star bg-slate-300"
                                                     aria-label="{{ $i }} star" aria-current="true"></div>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-slate-600 mb-6">{{ $totalReviews }} đánh giá</div>

                                    <div class="space-y-2">
                                        @for ($star = 5; $star >= 1; $star--)
                                            @php
                                                $count = $ratingDistribution[$star] ?? 0;
                                                $percentage =
                                                    $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm w-8">{{ $star }}★</span>
                                                <progress class="progress w-full" value="{{ $percentage }}"
                                                          max="100"></progress>
                                                <span class="text-sm w-8 text-right">{{ $count }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                @if ($evaluates->count() > 0)
                                    <div class="space-y-6">
                                        @foreach ($evaluates->take(5) as $evaluate)
                                            <div class="border-b border-slate-300 pb-6">
                                                <div class="flex items-start gap-4">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-gray-300 text-slate-600 rounded-full w-12">
                                                            @if ($user->avatar)
                                                                <img src="{{ $user->avatar }}"
                                                                     alt="{{ $user->name }}"
                                                                     class="object-cover w-full h-full">
                                                            @else
                                                                <img src="{{ asset('/images/default-user-icon.png') }}"
                                                                     alt="{{ $user->name }}"
                                                                     class="object-cover w-full h-full">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                                <span
                                                                    class="font-semibold">{{ $evaluate->user->name ?? 'Khách hàng' }}</span>
                                                            <div class="rating rating-sm">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <div class="mask mask-star {{ $i <= $evaluate->star_rating ? 'bg-orange-400' : 'bg-slate-300' }}"
                                                                         aria-label="{{ $i }} star"
                                                                         aria-current="true">
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                            <span
                                                                class="text-slate-500 text-sm">{{ $evaluate->created_at ? $evaluate->created_at->format('Y-m-d') : '' }}</span>
                                                        </div>
                                                        <p class="text-gray-700 mb-3">{{ $evaluate->comment }}</p>
                                                        @if ($evaluate->seller_rating)
                                                            <div class="text-sm text-slate-500 mb-2">
                                                                Đánh giá người bán:
                                                                <div class="rating rating-xs inline-block ml-1">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <div class="mask mask-star {{ $i <= $evaluate->seller_rating ? 'bg-orange-400' : 'bg-slate-300' }}"
                                                                             aria-label="{{ $i }} star"
                                                                             aria-current="true">
                                                                        </div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div class="text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đánh giá</h3>
                                            <p class="mt-1 text-sm text-gray-500">Hãy là người đầu tiên đánh giá sản
                                                phẩm này!</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-base font-bold text-gray-600 pt-4 mb-4">Sản phẩm này cũng phổ biến</div>
        <div class="swiper popularProductsSwiper">
            <div class="swiper-wrapper">
                @foreach ($product_category as $item)
                    @php
                        $thumb = optional($item->images->first())->image_url;

                        $itemAuction = null;
                        $itemBids = 0;
                        $itemCurrentPrice = $item->price;
                        $itemTimeLeft = null;

                        if ($item->type_sale === ($typeSale['AUCTION'] ?? 2)) {
                            $itemAuction = \App\Models\Auction::where('product_id', $item->id)->first();

                            if ($itemAuction) {
                                $itemBids = $itemAuction->bids()->count();
                                $highestBid = $itemAuction->bids()->orderBy('bid_price', 'desc')->first();
                                $itemCurrentPrice = $highestBid
                                    ? $highestBid->bid_price
                                    : $itemAuction->start_price;

                                $now = \Carbon\Carbon::now();
                                $endTime = \Carbon\Carbon::parse($itemAuction->end_time);

                                if ($endTime->gt($now)) {
                                    $diff = $now->diff($endTime);
                                    if ($diff->days > 0) {
                                        $itemTimeLeft = $diff->days . ' ngày ' . $diff->h . ' giờ';
                                    } else {
                                        $itemTimeLeft = $diff->h . ' giờ ' . $diff->i . ' phút';
                                    }
                                } else {
                                    $itemTimeLeft = 'Đã kết thúc';
                                }
                            }
                        }
                    @endphp
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('products.show', $item) }}" class="block">
                                <img class="w-full h-48 object-cover"
                                     src="{{ $thumb ? asset('storage/' . $thumb) : asset('images/default-user-icon.png') }}"
                                     alt="{{ $item->name }}" />
                            </a>
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-6 line-clamp-2 min-h-[3.5rem]">
                                    {{ $item->name }}
                                </h3>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between items-center">

                                        @if ($item->type_sale === ($typeSale['AUCTION'] ?? 2))
                                            <span class="text-sm text-gray-600">Giá hiện tại:</span>
                                            <span class="text-xl font-bold text-green-600">
                                                    {{ number_format($itemCurrentPrice, 0, ',', '.') }} ₫
                                                </span>
                                        @else
                                            <span class="text-sm text-gray-600">Giá bán:</span>
                                            <span class="text-xl font-bold text-red-600">
                                                    {{ number_format($item->price, 0, ',', '.') }} ₫
                                                </span>
                                        @endif

                                    </div>

                                    @if ($item->type_sale === ($typeSale['AUCTION'] ?? 2) && $itemAuction)
                                        <div class="flex justify-between items-center">
                                            @if ($itemTimeLeft && $itemTimeLeft !== 'Đã kết thúc')
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                         class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    <span>{{ $itemTimeLeft }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                     class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                </svg>
                                                <span>{{ $itemBids }} lượt</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('products.show', $item) }}"
                                   class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg text-center block transition-colors">
                                    @if ($item->type_sale === ($typeSale['AUCTION'] ?? 2))
                                        <button class="btn btn-neutral w-full">Tham gia đấu giá</button>
                                    @else
                                        <button class="btn btn-neutral w-full">Mua ngay</button>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-button-next !text-gray-600 !w-8 !h-8 !bg-white !rounded-full !shadow-md"></div>
            <div class="swiper-button-prev !text-gray-600 !w-8 !h-8 !bg-white !rounded-full !shadow-md"></div>
            <div class="swiper-pagination !bottom-0 !relative !mt-4"></div>
        </div>
    </div>

    @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2))
        @php
            $userHasBidded = isset($auctionData['auction']) && auth()->check()
                ? $auctionData['auction']->bids()->where('user_id', auth()->id())->exists()
                : false;
        @endphp
        <dialog id="bid_confirmation_modal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Xác nhận đấu giá</h3>
                <div class="space-y-4">
                    @unless ($userHasBidded)
                        <div role="alert" class="alert alert-warning alert-outline">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-yellow-700 font-medium">Phí tham gia đấu giá</span>
                            </div>
                        </div>
                    @endunless

                    @unless ($userHasBidded)
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Số coin cần trừ:</span>
                                <span class="font-semibold">{{ $coinBindProductAuction }} coin</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Giá trị 1 coin:</span>
                                <span class="font-semibold">{{ number_format($priceOneCoin, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Số coin hiện tại:</span>
                                    <span class="font-semibold">{{ $user->current_balance ?? 0 }} coin</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-800 font-medium">Phí:</span>
                                    <span class="text-red-600 font-bold text-sm">- {{ number_format($coinBindProductAuction, 0, ',', '.') }} coin</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-800 font-medium">Số dư sau giao dịch:</span>
                                    <span class="font-bold text-base">{{ number_format(($user->current_balance ?? 0) - $coinBindProductAuction, 0, ',', '.') }} coin</span>
                                </div>
                            </div>
                        </div>
                    @endunless

                    <div class="text-sm text-gray-600">
                        <p>Bạn có chắc chắn muốn tham gia đấu giá sản phẩm này?</p>
                        <p class="mt-1">Phí sẽ được trừ từ tài khoản của bạn ngay lập tức.</p>
                    </div>
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn btn-outline" onclick="closeBidConfirmation()">Hủy</button>
                    </form>
                    <button class="btn btn-neutral" onclick="confirmBid()">Xác nhận đấu giá</button>
                </div>
            </div>
        </dialog>
    @endif

    @include('pages.products.script')
@endsection
