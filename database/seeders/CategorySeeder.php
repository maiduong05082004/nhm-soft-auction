<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
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
                'status' => 'active',
            ],
            [
                'name' => 'Thời trang',
                'slug' => 'thoi-trang',
                'description' => 'Quần áo, giày dép, phụ kiện',
                'status' => 'active',
            ],
            [
                'name' => 'Nhà cửa',
                'slug' => 'nha-cua',
                'description' => 'Đồ gia dụng, nội thất',
                'status' => 'active',
            ],
            [
                'name' => 'Sách',
                'slug' => 'sach',
                'description' => 'Sách vở, tài liệu học tập',
                'status' => 'active',
            ],
            [
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'description' => 'Dụng cụ thể thao, đồ tập',
                'status' => 'active',
            ],
            [
                'name' => 'Mỹ phẩm',
                'slug' => 'my-pham',
                'description' => 'Mỹ phẩm, chăm sóc sắc đẹp',
                'status' => 'active',
            ],
            [
                'name' => 'Đồ chơi',
                'slug' => 'do-choi',
                'description' => 'Đồ chơi trẻ em, mô hình',
                'status' => 'active',
            ],
            [
                'name' => 'Ô tô',
                'slug' => 'o-to',
                'description' => 'Xe ô tô, phụ tùng xe',
                'status' => 'active',
            ],
            [
                'name' => 'Nghệ thuật',
                'slug' => 'nghe-thuat',
                'description' => 'Tranh vẽ, tác phẩm nghệ thuật',
                'status' => 'active',
            ],
            [
                'name' => 'Đồ cổ',
                'slug' => 'do-co',
                'description' => 'Đồ cổ, đồ sưu tầm',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
