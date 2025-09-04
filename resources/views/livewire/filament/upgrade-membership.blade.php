@assets
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/upgrade-membership.js'])
@endassets

<x-filament::section>
    <div class="grid grid-cols-4">
        <div class="grid-cols-4">
            <div class="!w-[400px] bg-center bg-cover swiper-slide">
                <div
                    class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-5">
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
                        <div class="flex flex-col gap-4 mb-4">
                            {{-- Tiêu đề --}}
                            <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                {{ $currentplan->name }}
                            </h5>
                            {{-- Điểm --}}
                            <h6 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $currentplan->price }}
                                POINT
                                <span class="text-sm text-gray-500 dark:text-white">/ {{ $currentplan->duration }}
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
                                            Miễn phí đăng bán {{ $currentplan->config['max_products_per_month'] }}
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
                            Mua ngay
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-cols-4 flex flex-col gap-4 w-full">
            @if ($this->list->isEmpty())
                <x-filament::section>
                    <div class="text-center text-gray-500">Không có gói thành viên nào</div>
                </x-filament::section>
            @else
                <div id="membership_list" class="w-full py-12">
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
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                            </svg>
                                                            Giảm {{ $item->config['discount_percentage'] }} % khi mua
                                                            sản
                                                            phẩm
                                                        </li>
                                                    @endif
                                                    @if ($item->config['free_product_listing'])
                                                        <li class="flex items-center">
                                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                            </svg>
                                                            Đăng bán sản phẩm miễn phí
                                                        </li>
                                                    @elseif($item->config['max_products_per_month'] > 0)
                                                        <li class="flex items-center">
                                                            <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                                        <x-filament::button wire:key="{{ $item->id }}" class="w-full"
                                            color="success" icon="heroicon-m-check-badge"
                                            wire:click="onNextStep('{{ $item->id }}')">
                                            Mua ngay
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
</x-filament::section>
