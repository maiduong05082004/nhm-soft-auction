@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center">
                    <h1 class="text-2xl font-bold mb-6">QR Code Thanh Toán</h1>
                    <h3 class="text-lg font-semibold mb-2">Đơn hàng: {{ $orderDetail->code_orders }}</h3>
                    <div class="flex flex-col items-center">
                        @foreach ($payment as $qrpay)
                            <div class="flex gap-4 ms:flex-row flex-col">
                                <div class="">
                                    <div class="mb-6">
                                        <p class="text-gray-600">Tổng tiền: {{ number_format($qrpay->amount, 0, ',', '.') }}
                                            ₫
                                        </p>
                                    </div>
                                    <div class="p-6 bg-white border-2 border-gray-200 rounded-lg mb-6">
                                        <img src="{{ $qrpay->vietqr }}" alt="VietQR Code" class="w-80 h-80 object-contain">
                                    </div>
                                </div>
                                <div class="flex flex-col justify-center">
                                    @foreach ($qrpay->orders as $item)
                                        <div class="py-3 flex items-center gap-3 justify-center">
                                            <div class="avatar">
                                                <div class="w-12 h-12 rounded-md overflow-hidden">
                                                    @php $img = $item->product?->images?->first()?->image_url; @endphp
                                                    <img src="{{ $img ? \App\Utils\HelperFunc::generateURLFilePath($img) : asset('images/product_default.jpg') }}"
                                                        alt="{{ $item->product->name ?? 'Sản phẩm' }}">
                                                </div>
                                            </div>
                                            <div class="flex-1 ">
                                                <div class="text-sm font-medium line-clamp-1">
                                                    {{ $item->product->name ?? 'Sản phẩm' }}</div>
                                                <div class="text-xs text-[#6c6a69]">x{{ $item->quantity }}</div>

                                                <div class="text-sm font-semibold flex flex-col">
                                                    <span>{{ number_format($item->total ?? $item->price * $item->quantity, 0, ',', '.') }}
                                                        ₫</span>
                                                    <span class="text-green-600 text-xs">Số tiền có thể giảm do mã giảm
                                                        giá</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Hướng dẫn thanh toán:</h4>
                        <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                            <li>Mở ứng dụng ngân hàng trên điện thoại</li>
                            <li>Chọn tính năng "Quét mã QR"</li>
                            <li>Quét mã QR bên trên</li>
                            <li>Kiểm tra thông tin và xác nhận thanh toán</li>
                            <li>Lưu lại biên lai thanh toán</li>
                        </ol>
                    </div>

                    <div class="flex ms:flex-row flex-col gap-2 justify-center ms:space-x-4">
                        <form action="{{ route('payment.confirm', $orderDetail->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Xác nhận thanh toán
                            </button>
                        </form>

                        <a href="{{ route('cart.index') }}" class="btn btn-outline">
                            Quay lại giỏ hàng
                        </a>
                    </div>

                    <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <strong>Lưu ý:</strong> Sau khi thanh toán, vui lòng nhấn "Xác nhận đã thanh toán" để cập nhật
                            trạng thái.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
