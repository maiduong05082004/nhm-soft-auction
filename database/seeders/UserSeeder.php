<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@auction.com',
                'password' => Hash::make('password'),
                'current_balance' => 10000,
                'reputation' => 100,
                'membership' => true,
                'phone' => '0123456789',
                'address' => 'Hà Nội, Việt Nam',
            ],
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 5000,
                'reputation' => 85,
                'membership' => true,
                'phone' => '0987654321',
                'address' => 'TP.HCM, Việt Nam',
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 3000,
                'reputation' => 70,
                'membership' => false,
                'phone' => '0369852147',
                'address' => 'Đà Nẵng, Việt Nam',
            ],
            [
                'name' => 'Lê Văn Cường',
                'email' => 'levancuong@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 7500,
                'reputation' => 95,
                'membership' => true,
                'phone' => '0521478963',
                'address' => 'Hải Phòng, Việt Nam',
            ],
            [
                'name' => 'Phạm Thị Dung',
                'email' => 'phamthidung@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 2000,
                'reputation' => 60,
                'membership' => false,
                'phone' => '0741236985',
                'address' => 'Cần Thơ, Việt Nam',
            ],
            [
                'name' => 'Hoàng Văn Em',
                'email' => 'hoangvanem@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 4500,
                'reputation' => 80,
                'membership' => true,
                'phone' => '0852369741',
                'address' => 'Nha Trang, Việt Nam',
            ],
            [
                'name' => 'Vũ Thị Phương',
                'email' => 'vuthiphuong@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 6000,
                'reputation' => 90,
                'membership' => true,
                'phone' => '0963258741',
                'address' => 'Huế, Việt Nam',
            ],
            [
                'name' => 'Đặng Văn Giang',
                'email' => 'dangvangiang@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 3500,
                'reputation' => 75,
                'membership' => false,
                'phone' => '0147852369',
                'address' => 'Vũng Tàu, Việt Nam',
            ],
            [
                'name' => 'Ngô Thị Hoa',
                'email' => 'ngothihoa@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 8000,
                'reputation' => 88,
                'membership' => true,
                'phone' => '0258963147',
                'address' => 'Quảng Ninh, Việt Nam',
            ],
            [
                'name' => 'Lý Văn Inh',
                'email' => 'lyvaninh@email.com',
                'password' => Hash::make('password'),
                'current_balance' => 1500,
                'reputation' => 50,
                'membership' => false,
                'phone' => '0369852147',
                'address' => 'Bình Dương, Việt Nam',
            ],
            // Tài khoản admin với mật khẩu đơn giản
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456'),
                'current_balance' => 999999,
                'reputation' => 100,
                'membership' => true,
                'phone' => '0909090909',
                'address' => 'Hà Nội, Việt Nam',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
