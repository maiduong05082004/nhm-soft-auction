@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <a href="{{ route('cart.index') }}" class="btn btn-neutral inline-flex items-center gap-2 text-sm hover:underline mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/>
        </svg>
        Quay lại giỏ hàng
    </a>

    <h1 class="text-3xl font-bold mb-3">Thanh toán</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <form id="checkout-form" action="{{ route('cart.process') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="selected" value="{{ $cartItems->pluck('product_id')->implode(',') }}">

                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-success">●</span>
                            <h2 class="card-title text-base font-bold text-[#6c6a69]">Thông tin giao hàng</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="form-control">
                                <div class="text-zinc-600"><span class="label-text">Họ và tên</span></div>
                                <input type="text" name="name" class="input input-bordered w-full" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            </div>

                            <div class="form-control">
                                <div class="text-zinc-600"><span class="label-text">Email</span></div>
                                <input type="email" name="email" class="input input-bordered w-full" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            </div>

                            <div class="form-control">
                                <div class="text-zinc-600"><span class="label-text">Số điện thoại</span></div>
                                <input type="text" name="phone" class="input input-bordered w-full" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            </div>

                            <div>
                                <div class="text-zinc-600">Địa chỉ</div>
                                <input type="text" name="address" class="input input-bordered w-full" placeholder="Số nhà, đường" value="{{ old('address', auth()->user()->address ?? '') }}" required>
                            </div>

                            <div>
                                <div><span class="text-zinc-600">Ghi chú đơn hàng</span></div>
                                <textarea name="note" class="textarea textarea-bordered w-full" rows="3" placeholder="Ghi chú về thời gian giao hàng, địa điểm, ...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-success">●</span>
                            <h2 class="card-title text-base font-bold text-[#6c6a69]">Phương thức thanh toán</h2>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer">
                                <input type="radio" name="payment_method" value="0" class="radio" checked>
                                <div>
                                    <div class="font-medium">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="text-sm text-[#6c6a69]">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer">
                                <input type="radio" name="payment_method" value="1" class="radio" @if(isset($hasCreditCard) && !$hasCreditCard) disabled @endif>
                                <div>
                                    <div class="font-medium">Chuyển khoản ngân hàng</div>
                                    <div class="text-sm text-[#6c6a69]">Chuyển khoản trước khi giao hàng</div>
                                    @if(isset($hasCreditCard) && !$hasCreditCard)
                                        <div class="text-xs text-red-500 mt-1">Chưa cấu hình chuyển khoản ngân hàng, vui lòng chọn COD.</div>
                                    @endif
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <aside class="lg:col-span-1 mt-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base font-bold text-[#6c6a69]">Đơn hàng của bạn</h3>

                    <div class="divide-y">
                        @foreach ($cartItems as $item)
                            <div class="py-3 flex items-center gap-3">
                                <div class="avatar">
                                    <div class="w-12 h-12 rounded-md overflow-hidden">
                                        @php $img = $item->product?->images?->first()?->image_url; @endphp
                                        <img src="{{ $img ? asset('storage/'.$img) : asset('images/default-avatar.png') }}" alt="{{ $item->product->name ?? 'Sản phẩm' }}">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium line-clamp-1">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                                    <div class="text-xs text-[#6c6a69]">x{{ $item->quantity }}</div>
                                </div>
                                <div class="text-sm font-semibold">
                                    {{ number_format($item->total ?? ($item->price*$item->quantity), 0, ',', '.') }} ₫
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#6c6a69] font-bold">Tạm tính</span>
                            <span class="font-semibold">{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#6c6a69] font-bold">Phí vận chuyển</span>
                            <span class="badge badge-success badge-outline">Miễn phí</span>
                        </div>
                        <div class="divider my-2"></div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Tổng cộng</span>
                            <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>

                    <div class="alert alert-success mt-3 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        <span>Thông tin của bạn được bảo mật an toàn</span>
                    </div>

                    <button type="submit" form="checkout-form" class="btn btn-neutral btn-block mt-4">
                        Đặt hàng
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection