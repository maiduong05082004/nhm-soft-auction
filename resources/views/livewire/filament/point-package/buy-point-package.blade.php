@assets
    @vite(['resources/css/app.css'])
@endassets

<x-filament::section>
    <div class="space-y-8">
        @if (!$nextStepBuy)
            <!-- Package Selection Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($list as $package)
                    <div
                        class="group relative bg-gradient-to-br from-white to-gray-50 hover:from-primary-50 hover:to-primary-100 
                                shadow-md hover:shadow-xl rounded-3xl p-6 border border-gray-100 hover:border-primary-200 
                                transition-all duration-300 transform hover:-translate-y-1 cursor-pointer
                                flex flex-col justify-between min-h-[280px]">

                        <!-- Package Header -->
                        <div class="space-y-4">
                            <div class="flex items-start justify-between">
                                <h3
                                    class="text-lg font-bold text-gray-800 group-hover:text-primary-700 transition-colors">
                                    {{ $package->name }}
                                </h3>
                                @if ($package->discount > 0)
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        -{{ $package->discount }}%
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                {!! $package->description !!}
                            </p>
                        </div>

                        <!-- Package Details -->
                        <div class="space-y-4">
                            <div class="text-center py-4 bg-white/70 rounded-2xl border border-gray-100">
                                <p class="text-3xl font-black text-primary-600 mb-1">
                                    {{ number_format($package->points) }}
                                </p>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                                    Điểm
                                </p>
                            </div>

                            <x-filament::button color="primary" size="lg"
                                class="w-full group-hover:scale-105 transition-transform"
                                wire:click="selectPackage('{{ $package->id }}')">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Chọn gói này
                                </span>
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Additional Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Lưu ý quan trọng</h4>
                        <p class="text-sm text-blue-700">Điểm sẽ được cộng vào tài khoản ngay sau khi thanh toán thành
                            công.</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Payment QR Section -->
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Selected Package Info -->
                    <div class="order-2 lg:order-1">
                        <div
                            class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-3xl p-8 shadow-xl border border-primary-200 h-full">
                            <div class="text-center mb-6">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-primary-800 mb-2">
                                    Gói đã chọn
                                </h3>
                                <p class="text-primary-700">
                                    Xác nhận thông tin gói điểm của bạn
                                </p>
                            </div>

                            <!-- Package Details -->
                            <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
                                <div class="text-center mb-4">
                                    <h4 class="text-xl font-bold text-gray-800 mb-2">
                                        {{ $pointPackage->name ?? 'Gói Premium' }}
                                    </h4>
                                    @if (($pointPackage->discount ?? 0) > 0)
                                        <span
                                            class="inline-block bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full mb-3">
                                            Giảm {{ $pointPackage->discount }}%
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                        <span class="text-sm font-medium text-gray-600">Điểm nhận được:</span>
                                        <span class="text-2xl font-black text-primary-600">
                                            {{ number_format($pointPackage->points ?? 0) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                        <span class="text-sm font-medium text-gray-600">Số tiền thanh toán:</span>
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ number_format($dataTransfer['totalPrice'] ?? 0) }} VNĐ
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <p class="text-sm text-blue-700 leading-relaxed">
                                        {!! $pointPackage->description ?? 'Gói điểm premium với nhiều ưu đãi hấp dẫn' !!}
                                    </p>
                                </div>
                            </div>

                            <!-- Benefits -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700">Điểm được cộng ngay lập tức</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700">Không có phí ẩn</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700">Bảo mật 100%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="order-1 lg:order-2">
                        <div
                            class="bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 shadow-2xl border border-gray-100 h-full">

                            <!-- Header -->
                            <div class="text-center mb-8">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4m6-4h2m-6 0V8m-6 4V8m0 0V4m0 4h2m-6 0h-2" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                                    Quét mã QR để thanh toán
                                </h3>
                                <p class="text-gray-600">
                                    Sử dụng ứng dụng ngân hàng để quét mã QR
                                </p>
                            </div>

                            <!-- QR Code Container -->
                            <div class="relative mb-8">
                                <div class="bg-white p-6 rounded-2xl shadow-lg border-2 border-dashed border-gray-200">
                                    <div class="relative">
                                        <img src="{{ $dataTransfer['urlBankQrcode'] }}" alt="QR code thanh toán"
                                            class="w-full max-w-64 mx-auto rounded-xl shadow-md" />

                                        <!-- QR Corner decorations -->
                                        <div
                                            class="absolute -top-2 -left-2 w-6 h-6 border-l-4 border-t-4 border-primary-500 rounded-tl-lg">
                                        </div>
                                        <div
                                            class="absolute -top-2 -right-2 w-6 h-6 border-r-4 border-t-4 border-primary-500 rounded-tr-lg">
                                        </div>
                                        <div
                                            class="absolute -bottom-2 -left-2 w-6 h-6 border-l-4 border-b-4 border-primary-500 rounded-bl-lg">
                                        </div>
                                        <div
                                            class="absolute -bottom-2 -right-2 w-6 h-6 border-r-4 border-b-4 border-primary-500 rounded-br-lg">
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading animation overlay -->
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-white/90 rounded-2xl opacity-0 hover:opacity-100 transition-opacity">
                                    <div class="text-center">
                                        <div
                                            class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto mb-2">
                                        </div>
                                        <p class="text-xs text-gray-600">Đang chờ thanh toán...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Payment Summary -->
                            <div
                                class="bg-gradient-to-r from-primary-50 to-primary-100 rounded-2xl p-4 mb-6 border border-primary-200">
                                <div class="text-center">
                                    <p class="text-sm text-primary-700 mb-1">Số tiền thanh toán</p>
                                    <p class="text-2xl font-black text-primary-800">
                                        {{ number_format($dataTransfer['totalPrice'] ?? 0) }} VNĐ
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <!-- Xác nhận giao dịch thành công -->
                                <x-filament::button color="success" size="lg" class="w-full"
                                    wire:click="confirmPaymentSuccess">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Xác nhận đã thanh toán thành công
                                    </span>
                                </x-filament::button>

                                <!-- Quay lại -->
                                <x-filament::button color="gray" outlined size="lg" class="w-full"
                                    wire:click="$set('nextStepBuy', false)">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Quay lại chọn gói khác
                                    </span>
                                </x-filament::button>

                                <button
                                    class="w-full text-sm text-gray-500 hover:text-primary-600 transition-colors py-2">
                                    Cần hỗ trợ? Liên hệ với chúng tôi
                                </button>
                            </div>

                            <!-- Security Badge -->
                            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Bảo mật SSL 256-bit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament::section>
