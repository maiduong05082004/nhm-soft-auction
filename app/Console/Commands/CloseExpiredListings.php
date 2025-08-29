<?php

namespace App\Console\Commands;

use App\Enums\Product\ProductStatus;
use App\Models\Product;
use App\Models\Config;
use App\Enums\Config\ConfigName;
use Carbon\Carbon;
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
    public function handle()
    {
        $now = Carbon::now();

        $products = Product::where('status', ProductStatus::ACTIVE)->get();

        $closedCount = 0;

        $defaultConfig = Config::where('config_key', ConfigName::DISPLAY_TIME_AFTER_AUCTION)->first();
        $extendedDuration = (int) ($defaultConfig?->config_value ?? 0);

        foreach ($products as $product) {

            $expireTime = Carbon::parse($product->end_time)->addDays($extendedDuration);

            if ($now->greaterThanOrEqualTo($expireTime)) {
                $product->status = ProductStatus::INACTIVE;
                $product->save();

                $this->info("Product ID {$product->id} ({$product->name}) đã được đóng bán.");
                $this->line("  - End time: " . $product->end_time);
                $this->line("  - Extended duration: {$extendedDuration} ngày");
                $this->line("  - Expire time: " . $expireTime->format('Y-m-d H:i:s'));
                $closedCount++;
            }
        }
        
        $this->info("Đã đóng {$closedCount} sản phẩm hết hạn.");
        $this->info('Đã xử lý tất cả sản phẩm hết hạn.');

        return Command::SUCCESS;
    }
}
