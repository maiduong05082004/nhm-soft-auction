@assets
@vite(['resources/css/app.css'])
@endassets

<div class="space-y-4">
    <x-filament::section icon="heroicon-m-currency-dollar">
        <x-slot name="heading">
            Tổng quan
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Số dư tiền nạp vào</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($current_balance, 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">VND</span>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Số điểm hiện tại</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_point'], 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">Point</span>
                </div>
            </div>
            {{-- <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng số tiền đã mua sản phẩm
                </div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_buy_product'], 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">VND</span>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng số tiền đã mua Trả giá</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_bid_product'], 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">VND</span>
                </div>
            </div> --}}
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Lịch sử giao dịch</x-slot>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                @foreach (\App\Enums\PaymentViewType::cases() as $type)
                    <button wire:click="setViewType('{{ $type->value }}')"
                        class="px-3 py-1 rounded {{ $viewType == $type->value ? 'bg-[#F54927] text-white' : 'bg-gray-100' }}">
                        {{ $type->label() }}
                    </button>
                @endforeach
            </div>

            {{-- Filament table render --}}
            {{ $this->table }}
        </div>
    </x-filament::section>

</div>
