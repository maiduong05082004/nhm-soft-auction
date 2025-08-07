<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bannerTypes = [
            [
                'name' => 'Banner chính',
                'status' => 'active',
            ],
            [
                'name' => 'Banner phụ',
                'status' => 'active',
            ],
            [
                'name' => 'Banner quảng cáo',
                'status' => 'active',
            ],
            [
                'name' => 'Banner sự kiện',
                'status' => 'active',
            ],
            [
                'name' => 'Banner khuyến mãi',
                'status' => 'active',
            ],
            [
                'name' => 'Banner tin tức',
                'status' => 'active',
            ],
            [
                'name' => 'Banner sản phẩm nổi bật',
                'status' => 'active',
            ],
            [
                'name' => 'Banner đấu giá hot',
                'status' => 'active',
            ],
            [
                'name' => 'Banner thông báo',
                'status' => 'active',
            ],
            [
                'name' => 'Banner liên hệ',
                'status' => 'active',
            ],
        ];

        foreach ($bannerTypes as $bannerType) {
            DB::table('banner_types')->insert($bannerType);
        }
    }
}
