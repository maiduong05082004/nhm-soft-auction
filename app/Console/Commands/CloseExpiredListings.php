<?php

namespace App\Console\Commands;

use App\Services\Products\ProductServiceInterface;
use Illuminate\Console\Command;

class CloseExpiredListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động đóng các sản phẩm sau thời gian hiển thị đã set ở DISPLAY_TIME_AFTER_AUCTION';

    /**
     * Execute the console command.
     */
    public function handle(ProductServiceInterface $productService)
    {
        $closedCount = $productService->closeExpiredListings();
        $this->info("Đã đóng {$closedCount} sản phẩm hết hạn.");
        $this->info('Đã xử lý tất cả sản phẩm hết hạn.');

        return Command::SUCCESS;
    }
}
