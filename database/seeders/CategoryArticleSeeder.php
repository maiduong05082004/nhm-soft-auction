<?php

namespace Database\Seeders;

use App\Models\CategoryArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            [
                'name' => 'Điện tử',
                'slug' => 'dien-tu',
                'description' => 'Các sản phẩm điện tử, công nghệ',
                'status' => 1,
            ],
            [
                'name' => 'Thời trang',
                'slug' => 'thoi-trang',
                'description' => 'Quần áo, giày dép, phụ kiện',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Nhà cửa',
                'slug' => 'nha-cua',
                'description' => 'Đồ gia dụng, nội thất',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Sách',
                'slug' => 'sach',
                'description' => 'Sách vở, tài liệu học tập',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'description' => 'Dụng cụ thể thao, đồ tập',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Mỹ phẩm',
                'slug' => 'my-pham',
                'description' => 'Mỹ phẩm, chăm sóc sắc đẹp',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Đồ chơi',
                'slug' => 'do-choi',
                'description' => 'Đồ chơi trẻ em, mô hình',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Ô tô',
                'slug' => 'o-to',
                'description' => 'Xe ô tô, phụ tùng xe',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Nghệ thuật',
                'slug' => 'nghe-thuat',
                'description' => 'Tranh vẽ, tác phẩm nghệ thuật',
                'status' =>  1,
                 
            ],
            [
                'name' => 'Đồ cổ',
                'slug' => 'do-co',
                'description' => 'Đồ cổ, đồ sưu tầm',
                'status' =>  1,
                 
            ],
        ];

        foreach ($categories as $category) {
            CategoryArticle::create($category);
        }
    }
}
