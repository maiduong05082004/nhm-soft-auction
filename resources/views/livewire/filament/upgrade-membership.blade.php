@assets
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/upgrade-membership.js'])
@endassets

<x-filament::section>
    @if (!$this->nextStepUpgrade)
        <div class="grid grid-cols-4">
            @if ($currentplan)
                <div class="col-span-4">
                    <div class="!w-[400px] bg-center bg-cover mx-auto ">
                        <div
                            class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                            <div class="p-5">
                                <p class="text-3xl text-center mb-5">Gói hiện tại</p>
                                @if (!empty($currentplan->badge))
                                    @php
                                        $badgeStyle = !empty($currentplan->badge_color)
                                            ? "background-color: {$currentplan->badge_color};"
                                            : "backgroud-color: '#ccc";
                                    @endphp
                                    <x-filament::badge size="sm" style="{{ $badgeStyle }}" class="mb-2">
                                        <p class="text-gray-600 dark:text-white">{{ $currentplan->badge }}</p>
                                    </x-filament::badge>
                                @endif
                                @if ($currentplan->is_testing)
                                    <x-filament::badge size="xs" color="info">Gói dùng
                                        thử</x-filament::badge>
                                @endif
                                <div class="flex flex-col gap-4 mb-4">
                                    {{-- Tiêu đề --}}
                                    <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                        {{ $currentplan->name }}
                                    </h5>
                                    {{-- Điểm --}}
                                    <h6 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        {{ $currentplan->price }}
                                        POINT
                                        <span class="text-sm text-gray-500 dark:text-white">/
                                            {{ $currentplan->duration }}
                                            tháng</span>
                                    </h6>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $currentplan->description }}
                                    </p>
                                    <div
                                        class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 p-2">
                                        <ul class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
                                            @if ($currentplan->config['featured_listing'])
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Sản phẩm được bán hiển thị ở vị trí nổi bật
                                                </li>
                                            @endif
                                            @if ($currentplan->config['priority_support'])
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Được ưu tiên hỗ trợ khi có vấn đề
                                                </li>
                                            @endif
                                            @if ($currentplan->config['discount_percentage'] > 0)
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Giảm {{ $currentplan->config['discount_percentage'] }} % khi mua sản
                                                    phẩm
                                                </li>
                                            @endif
                                            @if ($currentplan->config['free_product_listing'])
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Đăng bán sản phẩm miễn phí
                                                </li>
                                            @elseif($currentplan->config['max_products_per_month'] > 0)
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Miễn phí đăng bán
                                                    {{ $currentplan->config['max_products_per_month'] }}
                                                    sản
                                                    phẩm/ tháng
                                                </li>
                                            @endif
                                            @if ($currentplan->config['free_auction_participation'])
                                                <li class="flex currentplans-center">
                                                    <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                    </svg>
                                                    Tham gia trả giá miễn phí
                                                </li>
                                            @endif

                                        </ul>
                                    </div>
                                </div>
                                <!-- Khi nhấn vào nút Mua ngay, giá trị id của membership sẽ được gán cho Alpine -->
                                <x-filament::button wire:key="{{ $currentplan->id }}" class="w-full" color="success"
                                    icon="heroicon-m-check-badge" wire:click="onNextStep('{{ $currentplan->id }}')">
                                    Gia hạn gói
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-span-4 flex flex-col gap-4 w-full">
                <h3 class="text-3xl text-center my-2">Gói có thể nâng cấp</h3>
                @if ($this->list->isEmpty())
                    <x-filament::section>
                        <div class="text-center text-gray-500">Không có gói thành viên nào</div>
                    </x-filament::section>
                @else
                    <div id="membership_list" class="w-full">
                        <div class="swiper-wrapper">
                            @foreach ($this->list as $item)
                                <div class="!w-[400px] bg-center bg-cover swiper-slide">
                                    <div
                                        class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                        <div class="p-5">

                                            @if (!empty($item->badge))
                                                @php
                                                    $badgeStyle = !empty($item->badge_color)
                                                        ? "background-color: {$item->badge_color};"
                                                        : "backgroud-color: '#ccc";
                                                @endphp
                                                <x-filament::badge size="sm" style="{{ $badgeStyle }}"
                                                    class="mb-2">
                                                    <p class="text-gray-600 dark:text-white">{{ $item->badge }}</p>
                                                </x-filament::badge>
                                            @endif
                                            <div class="flex flex-col gap-4 mb-4">
                                                {{-- Tiêu đề --}}
                                                <h5
                                                    class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                                    {{ $item->name }}
                                                </h5>
                                                {{-- Điểm --}}
                                                <h6 class="text-3xl font-bold text-gray-900 dark:text-white">
                                                    {{ $item->price }}
                                                    POINT
                                                    <span class="text-sm text-gray-500 dark:text-white">/
                                                        {{ $item->duration }}
                                                        tháng</span>
                                                </h6>
                                                <p class="text-gray-900 dark:text-white">
                                                    {{ $item->description }}
                                                </p>
                                                <div
                                                    class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 p-2">
                                                    <ul
                                                        class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
                                                        @if ($item->config['featured_listing'])
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Sản phẩm được bán hiển thị ở vị trí nổi bật
                                                            </li>
                                                        @endif
                                                        @if ($item->config['priority_support'])
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Được ưu tiên hỗ trợ khi có vấn đề
                                                            </li>
                                                        @endif
                                                        @if ($item->config['discount_percentage'] > 0)
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Giảm {{ $item->config['discount_percentage'] }} % khi
                                                                mua
                                                                sản
                                                                phẩm
                                                            </li>
                                                        @endif
                                                        @if ($item->config['free_product_listing'])
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Đăng bán sản phẩm miễn phí
                                                            </li>
                                                        @elseif($item->config['max_products_per_month'] > 0)
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Miễn phí đăng bán
                                                                {{ $item->config['max_products_per_month'] }}
                                                                sản
                                                                phẩm/ tháng
                                                            </li>
                                                        @endif
                                                        @if ($item->config['free_auction_participation'])
                                                            <li class="flex items-center">
                                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                                </svg>
                                                                Tham gia trả giá miễn phí
                                                            </li>
                                                        @endif

                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Khi nhấn vào nút Mua ngay, giá trị id của membership sẽ được gán cho Alpine -->
                                            <x-filament::button wire:key="{{ $item->id }}" class="w-full"
                                                color="success" icon="heroicon-m-check-badge"
                                                wire:click="onNextStep('{{ $item->id }}')">
                                                Nâng cấp ngay
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <x-slot name="heading">
            Thanh toán nâng cấp gói thành viên
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="order-2 xl:order-1">
                <div x-data="{ loading: true }" class="relative w-full max-w-md mx-auto aspect-square"
                    x-on:payment-success.window="setTimeout(() => { $wire.redirectAfterSuccess() }, 1000)">

                    @if ($paymentSuccess)
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-center bg-green-50 rounded-lg border-2 border-green-200">
                            <div class="text-green-600 mb-3">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-green-800 text-center px-4">Thanh toán
                                thành công!</h3>
                            <p class="text-sm text-green-600 text-center mt-1">Đang chuyển hướng...</p>
                        </div>
                    @else
                        <!-- Loading indicator -->
                        <template x-if="loading">
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-lg">
                                <x-filament::loading-indicator class="w-6 h-6 text-primary-600" />
                            </div>
                        </template>

                        <!-- QR Code -->
                        <img src="{{ $dataTransfer['urlBankQrcode'] }}" alt="QR Code"
                            class="w-full h-full object-contain rounded-lg shadow-sm"
                            x-bind:class="{ 'opacity-0': loading, 'opacity-100': !loading }"
                            x-on:load="loading = false" x-on:error="loading = false"
                            style="transition: opacity 0.3s ease;" />
                    @endif
                </div>
            </div>
            <div class="order-1 xl:order-2">
                <div
                    class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-fit">
                    <div class="p-5">
                        @if (!empty($membershipPay->badge))
                            @php
                                $badgeStyle = !empty($membershipPay->badge_color)
                                    ? "background-color: {$membershipPay->badge_color};"
                                    : "backgroud-color: '#ccc";
                            @endphp
                            <x-filament::badge size="sm" style="{{ $badgeStyle }}" class="mb-2">
                                <p class="text-gray-600 dark:text-white">{{ $membershipPay->badge }}</p>
                            </x-filament::badge>
                        @endif
                        @if ($membershipPay->is_testing)
                            <x-filament::badge size="xs" class="my-2" color="info">Gói dùng
                                thử</x-filament::badge>
                        @endif
                        <div class="flex flex-col gap-4 mb-4">
                            {{-- Tiêu đề --}}
                            <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                {{ $membershipPay->name }}
                            </h5>
                            {{-- Điểm --}}
                            <h6 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $membershipPay->price }}
                                POINT
                                <span class="text-sm text-gray-500 dark:text-white">/ {{ $membershipPay->duration }}
                                    tháng</span>
                            </h6>
                            <p class="text-gray-900 dark:text-white">
                                {{ $membershipPay->description }}
                            </p>
                            <div
                                class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 p-2">
                                <ul class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
                                    @if ($membershipPay->config['featured_listing'])
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Sản phẩm được bán hiển thị ở vị trí nổi bật
                                        </li>
                                    @endif
                                    @if ($membershipPay->config['priority_support'])
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Được ưu tiên hỗ trợ khi có vấn đề
                                        </li>
                                    @endif
                                    @if ($membershipPay->config['discount_percentage'] > 0)
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Giảm {{ $membershipPay->config['discount_percentage'] }} % khi mua sản phẩm
                                        </li>
                                    @endif
                                    @if ($membershipPay->config['free_product_listing'])
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Đăng bán sản phẩm miễn phí
                                        </li>
                                    @elseif($membershipPay->config['max_products_per_month'] > 0)
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Miễn phí đăng bán {{ $membershipPay->config['max_products_per_month'] }}
                                            sản
                                            phẩm/ tháng
                                        </li>
                                    @endif
                                    @if ($membershipPay->config['free_auction_participation'])
                                        <li class="flex items-center">
                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            Tham gia Trả giá miễn phí
                                        </li>
                                    @endif

                                </ul>
                            </div>
                        </div>

                        <!-- Payment Status Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-600"
                            wire:poll.2s="refreshOrder">

                            <div class="text-center">
                                @if ($status == \App\Enums\Membership\MembershipTransactionStatus::WAITING->value)
                                    <div class="flex flex-col items-center gap-2 sm:gap-3">
                                        <span
                                            class="text-yellow-600 font-semibold flex items-center justify-center gap-2 text-sm sm:text-base">
                                            <span class="text-lg sm:text-xl">⏳</span>
                                            Đang chờ thanh toán...
                                        </span>
                                        @if (!empty($dataTransfer['expiredAt']))
                                            <div class="bg-white dark:bg-gray-800 px-3 py-2 rounded-md border"
                                                x-data="{ remain: {{ max(0, $dataTransfer['expiredAt'] - now()->timestamp) }} }" x-init="setInterval(() => { if (remain > 0) remain--; }, 1000)">
                                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                                    Hết hạn sau:
                                                    <span
                                                        class="font-mono font-semibold text-red-600 dark:text-red-400"
                                                        x-text="`${String(Math.floor(remain / 60)).padStart(2, '0')}:${String(remain % 60).padStart(2, '0')}`">
                                                    </span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif ($status == \App\Enums\Membership\MembershipTransactionStatus::ACTIVE->value)
                                    <span
                                        class="text-green-600 font-semibold flex items-center justify-center gap-2 text-sm sm:text-base">
                                        <span class="text-lg sm:text-xl">✅</span>
                                        Thanh toán thành công!
                                    </span>
                                @else
                                    <span
                                        class="text-red-600 font-semibold flex items-center justify-center gap-2 text-sm sm:text-base">
                                        <span class="text-lg sm:text-xl">❌</span>
                                        <span class="text-center">Thanh toán thất bại hoặc đã hủy</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament::section>
