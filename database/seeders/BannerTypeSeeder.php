<?php

namespace Database\Seeders;

use App\Enums\BannerType;
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
            // [
            //     'name' => BannerType::PRIMARY_HOME,
            //     'description' => 'Banner đầu trang chủ, banner chính của trang web khi người dùng truy cập ( chỉ có thể đặt duy nhất 1 )'
            // ],
            // [
            //     'name' => BannerType::SIDEBAR_HOME,
            //     'description' => 'Banner phía trái trang chủ (đặt tối thiểu 6 banner và tối đa 12 banner)'
            // ],

            // [
            //     'name' => BannerType::CONTENT_HOME,
            //     'description' => 'Banner trong phần thân trang chủ ( đặt tối thiểu 6 banner và tối đa 12 banner )'
            // ],

            // [
            //     'name' => BannerType::PRIMARY_NEWS,
            //     'description' => 'Banner chính trang tin tức ( banner chính khi người dùng trup cập trang tin tức, chỉ có thể đặt duy nhất 1 )'
            // ],
            // [
            //     'name' => BannerType::SIDEBAR_ARTICLE,
            //     'description' => 'Banner nhỏ phần sidebar bên phải bài viết (chỉ có thể đặt duy nhất 1)'
            // ],
        ];

        foreach ($bannerTypes as $bannerType) {
            DB::table('banner_types')->insert($bannerType);
        }
    }
}
