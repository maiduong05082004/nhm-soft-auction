<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderDetail;

class EvaluateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $order_details = OrderDetail::all();
        $evaluates = [
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 5,
                'comment' => 'Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 4,
                'comment' => 'MacBook Air M2 hoạt động mượt mà, pin trâu, rất hài lòng!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 5,
                'comment' => 'Giày Nike Air Jordan 1 đẹp, chất lượng tốt, đúng size!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 4,
                'comment' => 'Sofa đẹp, chất liệu tốt, phù hợp với phòng khách!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 5,
                'comment' => 'Sách Đắc Nhân Tâm rất hay, nội dung bổ ích!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 4,
                'comment' => 'Bóng đá Adidas chất lượng tốt, đá rất êm!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 5,
                'comment' => 'Kem dưỡng ẩm La Mer hiệu quả, da mịn màng hơn!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 4,
                'comment' => 'Mô hình xe Ferrari đẹp, chi tiết tinh xảo!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
                'star_rating' => 5,
                'comment' => 'Tranh sơn dầu đẹp, màu sắc hài hòa, rất thích!',
                'status' => 'active',
            ],
            [
                'user_id' => $users->random()->first()->id,
                'product_id' => $products->random()->first()->id,
                'order_detail_id' => $order_details->random()->first()->id,
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
