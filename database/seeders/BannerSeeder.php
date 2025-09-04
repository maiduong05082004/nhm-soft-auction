<?php

namespace Database\Seeders;

use App\Models\BannerType;
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
        $bannerType = BannerType::all();
        $banners = [
            [
                'name' => 'Banner chào mừng',
                'url_image' => 'banners/welcome-banner.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Khuyến mãi mùa hè',
                'url_image' => 'banners/summer-sale.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Sản phẩm nổi bật',
                'url_image' => 'banners/featured-products.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Trả giá hot',
                'url_image' => 'banners/hot-auctions.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Tin tức mới',
                'url_image' => 'banners/news-banner.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Sự kiện đặc biệt',
                'url_image' => 'banners/special-event.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Quảng cáo sản phẩm',
                'url_image' => 'banners/product-ad.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Banner phụ trợ',
                'url_image' => 'banners/support-banner.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Thông báo quan trọng',
                'url_image' => 'banners/important-notice.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
            [
                'name' => 'Liên hệ hỗ trợ',
                'url_image' => 'banners/contact-support.jpg',
                'banner_type_id' => $bannerType->random()->id,
            ],
        ];

        foreach ($banners as $banner) {
            DB::table('banners')->insert($banner);
        }
    }
}
