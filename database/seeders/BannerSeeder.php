<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'name' => 'Banner chào mừng',
                'url_image' => 'banners/welcome-banner.jpg',
                'banner_type_id' => 1,
            ],
            [
                'name' => 'Khuyến mãi mùa hè',
                'url_image' => 'banners/summer-sale.jpg',
                'banner_type_id' => 5,
            ],
            [
                'name' => 'Sản phẩm nổi bật',
                'url_image' => 'banners/featured-products.jpg',
                'banner_type_id' => 7,
            ],
            [
                'name' => 'Đấu giá hot',
                'url_image' => 'banners/hot-auctions.jpg',
                'banner_type_id' => 8,
            ],
            [
                'name' => 'Tin tức mới',
                'url_image' => 'banners/news-banner.jpg',
                'banner_type_id' => 6,
            ],
            [
                'name' => 'Sự kiện đặc biệt',
                'url_image' => 'banners/special-event.jpg',
                'banner_type_id' => 4,
            ],
            [
                'name' => 'Quảng cáo sản phẩm',
                'url_image' => 'banners/product-ad.jpg',
                'banner_type_id' => 3,
            ],
            [
                'name' => 'Banner phụ trợ',
                'url_image' => 'banners/support-banner.jpg',
                'banner_type_id' => 2,
            ],
            [
                'name' => 'Thông báo quan trọng',
                'url_image' => 'banners/important-notice.jpg',
                'banner_type_id' => 9,
            ],
            [
                'name' => 'Liên hệ hỗ trợ',
                'url_image' => 'banners/contact-support.jpg',
                'banner_type_id' => 10,
            ],
        ];

        foreach ($banners as $banner) {
            DB::table('banners')->insert($banner);
        }
    }
}
