<?php

namespace Database\Seeders;

use App\Enums\Config\ConfigName;
use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Config::query()->create([
//            'config_key' => ConfigName::ADMIN_ACCOUNT_BANK_NAME,
//            'config_value' => "BUI HUY ANH",
//            'description' => 'Tên chủ thể ngân hàng chính của hệ thống dùng để thanh toán',
//        ]);
//        Config::query()->create([
//            'config_key' => ConfigName::ADMIN_ACCOUNT_BANK_ACCOUNT,
//            'config_value' => 19034110877016,
//            'description' => 'STK ngân hàng chính của hệ thống dùng để thanh toán'
//        ]);
//        Config::query()->create([
//            'config_key' => ConfigName::ADMIN_ACCOUNT_BANK_BIN,
//            'config_value' => 970407,
//            'description' => 'Mã Bin ngân hàng chính của hệ thống dùng để thanh toán'
//        ]);
        Config::query()->create([
            'config_key' => ConfigName::COIN_POST_PRODUCT_SALE,
            'config_value' => 1,
            'description' => 'Số coin cần để đăng bán sản phẩm (là số)',
        ]);
        Config::query()->create([
            'config_key' => ConfigName::COIN_POST_PRODUCT_AUCTION,
            'config_value' => 1,
            'description' => 'Số coin cần để đăng đấu giá sản phẩm (là số)',
        ]);
        Config::query()->create([
            'config_key' => ConfigName::PRICE_ONE_COIN,
            'config_value' => 100000,
            'description' => 'Giá trị của 1 coin (là số, đơn vị là VNĐ)',
        ]);
    }
}
