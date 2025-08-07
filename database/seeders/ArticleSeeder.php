<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Hướng dẫn đấu giá online cho người mới',
                'slug' => 'huong-dan-dau-gia-online-cho-nguoi-moi',
                'content' => 'Bài viết hướng dẫn chi tiết cách tham gia đấu giá online cho những người mới bắt đầu...',
                'image' => 'articles/auction-guide.jpg',
                'view' => 150,
                'user_id' => 1,
                'sort' => 1,
                'status' => 'published',
            ],
            [
                'title' => 'Top 10 sản phẩm đấu giá hot nhất tháng',
                'slug' => 'top-10-san-pham-dau-gia-hot-nhat-thang',
                'content' => 'Danh sách 10 sản phẩm đấu giá được quan tâm nhiều nhất trong tháng...',
                'image' => 'articles/hot-products.jpg',
                'view' => 320,
                'user_id' => 1,
                'sort' => 2,
                'status' => 'published',
            ],
            [
                'title' => 'Chiến lược đấu giá thông minh',
                'slug' => 'chien-luoc-dau-gia-thong-minh',
                'content' => 'Những chiến lược và mẹo đấu giá thông minh để giành chiến thắng...',
                'image' => 'articles/auction-strategy.jpg',
                'view' => 280,
                'user_id' => 1,
                'sort' => 3,
                'status' => 'published',
            ],
            [
                'title' => 'Lịch sử đấu giá thú vị',
                'slug' => 'lich-su-dau-gia-thu-vi',
                'content' => 'Những câu chuyện thú vị về lịch sử đấu giá trên thế giới...',
                'image' => 'articles/auction-history.jpg',
                'view' => 95,
                'user_id' => 1,
                'sort' => 4,
                'status' => 'published',
            ],
            [
                'title' => 'Cách nhận biết hàng giả khi đấu giá',
                'slug' => 'cach-nhan-biet-hang-gia-khi-dau-gia',
                'content' => 'Hướng dẫn cách phân biệt hàng thật và hàng giả khi tham gia đấu giá...',
                'image' => 'articles/fake-detection.jpg',
                'view' => 420,
                'user_id' => 1,
                'sort' => 5,
                'status' => 'published',
            ],
            [
                'title' => 'Đấu giá nghệ thuật: Những điều cần biết',
                'slug' => 'dau-gia-nghe-thuat-nhung-dieu-can-biet',
                'content' => 'Kiến thức cơ bản về đấu giá các tác phẩm nghệ thuật...',
                'image' => 'articles/art-auction.jpg',
                'view' => 180,
                'user_id' => 1,
                'sort' => 6,
                'status' => 'published',
            ],
            [
                'title' => 'Bảo mật thông tin khi đấu giá online',
                'slug' => 'bao-mat-thong-tin-khi-dau-gia-online',
                'content' => 'Các biện pháp bảo mật thông tin cá nhân khi tham gia đấu giá online...',
                'image' => 'articles/security.jpg',
                'view' => 250,
                'user_id' => 1,
                'sort' => 7,
                'status' => 'published',
            ],
            [
                'title' => 'Đấu giá đồ cổ: Bí quyết thành công',
                'slug' => 'dau-gia-do-co-bi-quyet-thanh-cong',
                'content' => 'Những bí quyết để thành công khi đấu giá các món đồ cổ...',
                'image' => 'articles/antique-auction.jpg',
                'view' => 160,
                'user_id' => 1,
                'sort' => 8,
                'status' => 'published',
            ],
            [
                'title' => 'Xu hướng đấu giá 2024',
                'slug' => 'xu-huong-dau-gia-2024',
                'content' => 'Những xu hướng mới trong lĩnh vực đấu giá năm 2024...',
                'image' => 'articles/trends-2024.jpg',
                'view' => 300,
                'user_id' => 1,
                'sort' => 9,
                'status' => 'published',
            ],
            [
                'title' => 'Pháp lý trong đấu giá online',
                'slug' => 'phap-ly-trong-dau-gia-online',
                'content' => 'Những vấn đề pháp lý cần lưu ý khi tham gia đấu giá online...',
                'image' => 'articles/legal-issues.jpg',
                'view' => 120,
                'user_id' => 1,
                'sort' => 10,
                'status' => 'published',
            ],
        ];

        foreach ($articles as $article) {
            DB::table('articles')->insert($article);
        }
    }
}
