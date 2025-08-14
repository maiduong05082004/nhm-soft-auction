<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $auctions = [
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 20000000,
                'step_price' => 500000,
                'start_time' => now(),
                'end_time' => now()->addDays(7),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 30000000,
                'step_price' => 1000000,
                'start_time' => now(),
                'end_time' => now()->addDays(5),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 3000000,
                'step_price' => 100000,
                'start_time' => now(),
                'end_time' => now()->addDays(3),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 12000000,
                'step_price' => 500000,
                'start_time' => now(),
                'end_time' => now()->addDays(10),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 100000,
                'step_price' => 10000,
                'start_time' => now(),
                'end_time' => now()->addDays(2),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 400000,
                'step_price' => 25000,
                'start_time' => now(),
                'end_time' => now()->addDays(4),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 7000000,
                'step_price' => 200000,
                'start_time' => now(),
                'end_time' => now()->addDays(6),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 2000000,
                'step_price' => 100000,
                'start_time' => now(),
                'end_time' => now()->addDays(8),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 10000000,
                'step_price' => 500000,
                'start_time' => now(),
                'end_time' => now()->addDays(12),
                'status' => 'active',
            ],
            [
                'product_id' => $products->random()->first()->id,
                'start_price' => 750000000,
                'step_price' => 10000000,
                'start_time' => now(),
                'end_time' => now()->addDays(15),
                'status' => 'active',
            ],
        ];

        foreach ($auctions as $auction) {
            DB::table('auctions')->insert($auction);
        }
    }
}
