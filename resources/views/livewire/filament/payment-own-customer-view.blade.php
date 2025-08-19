<div class="space-y-4">
    <x-filament::section
        icon="heroicon-m-currency-dollar"
    >
        <x-slot name="heading">
            Tổng quan
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Số dư tiền nạp vào</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_rechange'], 0, ',', '.') }}
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
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng số tiền đã mua sản phẩm</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_buy_product'], 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">VND</span>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng số tiền đã mua đấu giá</div>
                <div class="text-3xl font-bold text-gray-700 dark:text-white">
                    {{ number_format($this->sumTransaction['sum_bid_product'], 0, ',', '.') }}
                    <span class="text-2xl text-gray-600 dark:text-gray-400">VND</span>
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Lịch sử giao dịch
        </x-slot>
    </x-filament::section>
</div>
