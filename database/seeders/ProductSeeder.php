<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'price' => 25000000,
                'description' => 'iPhone 15 Pro Max mới nhất với chip A17 Pro, camera 48MP',
                'view' => 150,
                'stock' => 5,
                'min_bid_amount' => 20000000,
                'max_bid_amount' => 30000000,
                'type_sale' => 'auction',
                'category_id' => 1,
                'start_time' => now(),
                'end_time' => now()->addDays(7),
                'status' => 'active',
            ],
            [
                'name' => 'MacBook Air M2',
                'slug' => 'macbook-air-m2',
                'price' => 35000000,
                'description' => 'MacBook Air với chip M2, màn hình 13.6 inch',
                'view' => 120,
                'stock' => 3,
                'min_bid_amount' => 30000000,
                'max_bid_amount' => 40000000,
                'type_sale' => 'auction',
                'category_id' => 1,
                'start_time' => now(),
                'end_time' => now()->addDays(5),
                'status' => 'active',
            ],
            [
                'name' => 'Nike Air Jordan 1',
                'slug' => 'nike-air-jordan-1',
                'price' => 3500000,
                'description' => 'Giày Nike Air Jordan 1 Retro High OG',
                'view' => 200,
                'stock' => 10,
                'min_bid_amount' => 3000000,
                'max_bid_amount' => 4000000,
                'type_sale' => 'sale',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Sofa phòng khách',
                'slug' => 'sofa-phong-khach',
                'price' => 15000000,
                'description' => 'Sofa phòng khách hiện đại, chất liệu da cao cấp',
                'view' => 80,
                'stock' => 2,
                'min_bid_amount' => 12000000,
                'max_bid_amount' => 18000000,
                'type_sale' => 'sale',
                'category_id' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Sách "Đắc Nhân Tâm"',
                'slug' => 'sach-dac-nhan-tam',
                'price' => 150000,
                'description' => 'Sách Đắc Nhân Tâm - Dale Carnegie',
                'view' => 300,
                'stock' => 50,
                'min_bid_amount' => 100000,
                'max_bid_amount' => 200000,
                'type_sale' => 'sale',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'Bóng đá Adidas',
                'slug' => 'bong-da-adidas',
                'price' => 500000,
                'description' => 'Bóng đá chính thức Adidas Champions League',
                'view' => 95,
                'stock' => 15,
                'min_bid_amount' => 400000,
                'max_bid_amount' => 600000,
                'type_sale' => 'sale',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Kem dưỡng ẩm La Mer',
                'slug' => 'kem-duong-am-la-mer',
                'price' => 8000000,
                'description' => 'Kem dưỡng ẩm cao cấp La Mer 50ml',
                'view' => 180,
                'stock' => 8,
                'min_bid_amount' => 7000000,
                'max_bid_amount' => 9000000,
                'type_sale' => 'sale',
                'category_id' => 6,
                'status' => 'active',
            ],
            [
                'name' => 'Mô hình xe Ferrari',
                'slug' => 'mo-hinh-xe-ferrari',
                'price' => 2500000,
                'description' => 'Mô hình xe Ferrari F40 tỷ lệ 1:18',
                'view' => 110,
                'stock' => 12,
                'min_bid_amount' => 2000000,
                'max_bid_amount' => 3000000,
                'type_sale' => 'sale',
                'category_id' => 7,
                'status' => 'active',
            ],
            [
                'name' => 'Tranh sơn dầu phong cảnh',
                'slug' => 'tranh-son-dau-phong-canh',
                'price' => 12000000,
                'description' => 'Tranh sơn dầu phong cảnh Việt Nam',
                'view' => 60,
                'stock' => 1,
                'min_bid_amount' => 10000000,
                'max_bid_amount' => 15000000,
                'type_sale' => 'auction',
                'category_id' => 9,
                'start_time' => now(),
                'end_time' => now()->addDays(10),
                'status' => 'active',
            ],
            [
                'name' => 'Đồng hồ Rolex Submariner',
                'slug' => 'dong-ho-rolex-submariner',
                'price' => 800000000,
                'description' => 'Đồng hồ Rolex Submariner phiên bản giới hạn',
                'view' => 500,
                'stock' => 1,
                'min_bid_amount' => 750000000,
                'max_bid_amount' => 850000000,
                'type_sale' => 'auction',
                'category_id' => 10,
                'start_time' => now(),
                'end_time' => now()->addDays(15),
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
