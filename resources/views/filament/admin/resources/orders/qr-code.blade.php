<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-6" style="padding: 20px;">
                    QR Code Thanh Toán
                </h1>

                <div class="inline-block p-6 bg-white border-2 border-gray-200 rounded-lg mb-6">
                    <img src="{{ $this->getVietQRUrl() }}" alt="VietQR Code" class="w-80 h-80 object-contain">
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

                <div class="flex justify-center space-x-4 gap-4">
                    <x-filament::button color="success" wire:click="confirmPayment" icon="heroicon-o-check-circle">
                        Xác nhận đã thanh toán
                    </x-filament::button>

                    <x-filament::button tag="a" color="gray"
                        href="{{ route('filament.admin.resources.orders.edit', ['record' => $this->record->id]) }}"
                        icon="heroicon-o-arrow-left">
                        Quay lại đơn hàng
                    </x-filament::button>
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
</x-filament-panels::page>
