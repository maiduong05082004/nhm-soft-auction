<x-filament-panels::page>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow p-6 sm:p-10">
            <h1 class="text-2xl font-bold text-gray-900 text-center mb-10">
                QR Code Thanh Toán
            </h1>

            @if (!empty($this->sellerBreakdowns))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach ($this->sellerBreakdowns as $seller)
                        <div class="border rounded-xl p-6 flex flex-col items-center text-center bg-gray-50">
                            <div class="mb-4">
                                <div class="text-sm text-gray-500">Thanh toán cho người bán</div>
                                <div class="font-semibold text-gray-900 text-lg">{{ $seller['seller_name'] }}</div>
                            </div>

                            @if ($seller['credit_card'])
                                <div class="p-4 bg-white border rounded-xl mb-4">
                                    <img 
                                        src="{{ $this->getVietQRUrl($seller['credit_card'], $seller['amount'], $seller['add_info']) }}" 
                                        alt="VietQR Code" 
                                        class="w-64 h-64 object-contain"
                                    >
                                </div>

                                <div class="text-sm text-gray-700 mb-1">
                                    Số tiền: 
                                    <span class="font-semibold text-base text-green-600">
                                        {{ number_format($seller['amount'], 0, ',', '.') }} ₫
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 italic">
                                    Nội dung: {{ $seller['add_info'] }}
                                </div>
                            @else
                                <div class="p-3 bg-yellow-50 text-yellow-800 rounded-lg w-full text-center">
                                    Người bán chưa cấu hình tài khoản ngân hàng.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-center">
                    Không có dữ liệu người bán cho đơn hàng này.
                </div>
            @endif

            <div class="mt-10 p-6 bg-blue-50 rounded-xl border border-blue-100">
                <h4 class="font-semibold text-blue-900 mb-3 text-lg">Hướng dẫn thanh toán:</h4>
                <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
                    <li>Mở ứng dụng ngân hàng trên điện thoại</li>
                    <li>Chọn tính năng "Quét mã QR"</li>
                    <li>Quét từng mã QR tương ứng từng người bán</li>
                    <li>Kiểm tra thông tin và xác nhận thanh toán</li>
                    <li>Lưu lại biên lai thanh toán</li>
                </ol>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                <x-filament::button color="success" wire:click="confirmPayment" icon="heroicon-o-check-circle" class="px-6 py-2 text-base">
                    Xác nhận đã thanh toán
                </x-filament::button>

                <x-filament::button tag="a" color="gray" href="{{ \App\Filament\Pages\MyOrdersPage::getUrl() }}" icon="heroicon-o-arrow-left" class="px-6 py-2 text-base">
                    Quay lại danh sách đơn hàng
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
