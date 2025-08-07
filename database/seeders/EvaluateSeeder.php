<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $evaluates = [
            [
                'user_id' => 2,
                'product_id' => 1,
                'order_id' => 1,
                'star_rating' => 5,
                'comment' => 'Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh!',
                'status' => 'active',
            ],
            [
                'user_id' => 3,
                'product_id' => 2,
                'order_id' => 2,
                'star_rating' => 4,
                'comment' => 'MacBook Air M2 hoạt động mượt mà, pin trâu, rất hài lòng!',
                'status' => 'active',
            ],
            [
                'user_id' => 4,
                'product_id' => 3,
                'order_id' => 3,
                'star_rating' => 5,
                'comment' => 'Giày Nike Air Jordan 1 đẹp, chất lượng tốt, đúng size!',
                'status' => 'active',
            ],
            [
                'user_id' => 5,
                'product_id' => 4,
                'order_id' => 4,
                'star_rating' => 4,
                'comment' => 'Sofa đẹp, chất liệu tốt, phù hợp với phòng khách!',
                'status' => 'active',
            ],
            [
                'user_id' => 6,
                'product_id' => 5,
                'order_id' => 5,
                'star_rating' => 5,
                'comment' => 'Sách Đắc Nhân Tâm rất hay, nội dung bổ ích!',
                'status' => 'active',
            ],
            [
                'user_id' => 7,
                'product_id' => 6,
                'order_id' => 6,
                'star_rating' => 4,
                'comment' => 'Bóng đá Adidas chất lượng tốt, đá rất êm!',
                'status' => 'active',
            ],
            [
                'user_id' => 8,
                'product_id' => 7,
                'order_id' => 7,
                'star_rating' => 5,
                'comment' => 'Kem dưỡng ẩm La Mer hiệu quả, da mịn màng hơn!',
                'status' => 'active',
            ],
            [
                'user_id' => 9,
                'product_id' => 8,
                'order_id' => 8,
                'star_rating' => 4,
                'comment' => 'Mô hình xe Ferrari đẹp, chi tiết tinh xảo!',
                'status' => 'active',
            ],
            [
                'user_id' => 10,
                'product_id' => 9,
                'order_id' => 9,
                'star_rating' => 5,
                'comment' => 'Tranh sơn dầu đẹp, màu sắc hài hòa, rất thích!',
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'product_id' => 10,
                'order_id' => 10,
                'star_rating' => 5,
                'comment' => 'Đồng hồ Rolex Submariner chính hãng, chất lượng tuyệt vời!',
                'status' => 'active',
            ],
        ];

        foreach ($evaluates as $evaluate) {
            DB::table('evaluate')->insert($evaluate);
        }
    }
}
