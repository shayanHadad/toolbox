<?php

namespace Database\Seeders;

use App\Models\WorkCategory;
use Illuminate\Database\Seeder;

class WorkCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'تمیزکاری',   'url' => 'cleaning'],
            ['category_name' => 'ساختمان',    'url' => 'building'],
            ['category_name' => 'اسباب‌کشی و باربری', 'url' => 'moving'],
            ['category_name' => 'خودرو',      'url' => 'car'],
            ['category_name' => 'تعمیرات اشیا', 'url' => 'objects-repair'],
            ['category_name' => 'سازمان‌ها', 'url' => 'organizations'],
            ['category_name' => 'سایر', 'url' => 'others'],
        ];


        foreach ($categories as $category) {
            WorkCategory::firstOrCreate(
                ['category_name' => $category['category_name']],
                ['url' => $category['url']]
            );
        }

        $this->command->info('دسته‌بندی‌ها ساخته شدند (' . count($categories) . ' مورد).');
    }
}
