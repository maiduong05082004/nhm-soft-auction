<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Số dư hiện tại</div>
        <div class="text-3xl font-bold text-gray-700 dark:text-white">
            {{ number_format($getRecord()->dynamic_current_balance, 0, ',', '.') }} 
            <span class="text-2xl text-gray-600 dark:text-gray-400">₫</span>
        </div>
        
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng tiền nạp</div>
        <div class="text-3xl font-bold text-gray-700 dark:text-white">
            {{ number_format($getRecord()->total_recharge, 0, ',', '.') }} 
            <span class="text-2xl text-gray-600 dark:text-gray-400">₫</span>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tổng tiền đã mua sản phẩm</div>
        <div class="text-3xl font-bold text-gray-700 dark:text-white">
            {{ number_format(abs($getRecord()->total_buy_product), 0, ',', '.') }} 
            <span class="text-2xl text-gray-600 dark:text-gray-400">₫</span>
        </div>
    </div>
</div>
