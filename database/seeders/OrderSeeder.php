<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'code_orders' => 'ORD001',
                'user_id' => 2,
                'email_receiver' => 'nguyenvanan@email.com',
                'ship_address' => '123 Nguyễn Huệ, TP.HCM',
                'payment_method' => 1,
                'shipping_fee' => 30000,
                'subtotal' => 25000000,
                'total' => 25030000,
                'note' => 'Giao hàng vào buổi sáng',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD002',
                'user_id' => 3,
                'email_receiver' => 'tranthibinh@email.com',
                'ship_address' => '456 Lê Lợi, Đà Nẵng',
                'payment_method' => 2,
                'shipping_fee' => 25000,
                'subtotal' => 35000000,
                'total' => 35025000,
                'note' => 'Giao hàng vào buổi chiều',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD003',
                'user_id' => 4,
                'email_receiver' => 'levancuong@email.com',
                'ship_address' => '789 Trần Phú, Hải Phòng',
                'payment_method' => 1,
                'shipping_fee' => 20000,
                'subtotal' => 3500000,
                'total' => 3520000,
                'note' => 'Giao hàng nhanh',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD004',
                'user_id' => 5,
                'email_receiver' => 'phamthidung@email.com',
                'ship_address' => '321 Võ Văn Tần, Cần Thơ',
                'payment_method' => 2,
                'shipping_fee' => 35000,
                'subtotal' => 15000000,
                'total' => 15035000,
                'note' => 'Giao hàng vào cuối tuần',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD005',
                'user_id' => 6,
                'email_receiver' => 'hoangvanem@email.com',
                'ship_address' => '654 Nguyễn Thị Minh Khai, Nha Trang',
                'payment_method' => 1,
                'shipping_fee' => 30000,
                'subtotal' => 150000,
                'total' => 180000,
                'note' => 'Giao hàng vào sáng sớm',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD006',
                'user_id' => 7,
                'email_receiver' => 'vuthiphuong@email.com',
                'ship_address' => '987 Phan Đình Phùng, Huế',
                'payment_method' => 2,
                'shipping_fee' => 25000,
                'subtotal' => 500000,
                'total' => 525000,
                'note' => 'Giao hàng vào buổi tối',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD007',
                'user_id' => 8,
                'email_receiver' => 'dangvangiang@email.com',
                'ship_address' => '147 Trần Hưng Đạo, Vũng Tàu',
                'payment_method' => 1,
                'shipping_fee' => 40000,
                'subtotal' => 8000000,
                'total' => 8040000,
                'note' => 'Giao hàng vào cuối tuần',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD008',
                'user_id' => 9,
                'email_receiver' => 'ngothihoa@email.com',
                'ship_address' => '258 Lê Thánh Tông, Quảng Ninh',
                'payment_method' => 2,
                'shipping_fee' => 20000,
                'subtotal' => 2500000,
                'total' => 2520000,
                'note' => 'Giao hàng nhanh',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD009',
                'user_id' => 10,
                'email_receiver' => 'lyvaninh@email.com',
                'ship_address' => '369 Đồng Khởi, Bình Dương',
                'payment_method' => 1,
                'shipping_fee' => 30000,
                'subtotal' => 12000000,
                'total' => 12030000,
                'note' => 'Giao hàng vào buổi sáng',
                'status' => 'completed',
            ],
            [
                'code_orders' => 'ORD010',
                'user_id' => 1,
                'email_receiver' => 'admin@auction.com',
                'ship_address' => '741 Nguyễn Trãi, Hà Nội',
                'payment_method' => 2,
                'shipping_fee' => 50000,
                'subtotal' => 80000000,
                'total' => 80050000,
                'note' => 'Giao hàng đặc biệt',
                'status' => 'completed',
            ],
        ];

        foreach ($orders as $order) {
            DB::table('orders')->insert($order);
        }
    }
}
