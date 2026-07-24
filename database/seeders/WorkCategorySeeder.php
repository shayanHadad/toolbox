<?php

namespace Database\Seeders;

use App\Models\WorkCategory;
use Illuminate\Database\Seeder;

class WorkCategorySeeder extends Seeder
{
    public function run(): void
    {
        // اینجا دیتای دلخواه خودت رو بذار. هر آیتم یه دسته‌بندیه.
        $categories = [
            ['category_name' => 'تمیزکاری',   'url' => 'cleaning'],
            ['category_name' => 'ساختمان',    'url' => 'building'],
            ['category_name' => 'اسباب‌کشی و حمل بار', 'url' => 'moving'],
            ['category_name' => 'خودرو',      'url' => 'car'],
            ['category_name' => 'تعمیرات اشیا', 'url' => 'objects-repair'],
            ['category_name' => 'سازمان‌ها', 'url' => 'organizations'],
            ['category_name' => 'سایر', 'url' => 'others'],
        ];


        foreach ($categories as $category) {
            // firstOrCreate یعنی اگه از قبل با همین اسم وجود داشت،
            // دوباره ساخته نمیشه و دیتا تکراری نمی‌خوره اگه seeder چندبار اجرا بشه.
            WorkCategory::firstOrCreate(
                ['category_name' => $category['category_name']],
                ['url' => $category['url']]
            );
        }

        $this->command->info('دسته‌بندی‌ها ساخته شدند (' . count($categories) . ' مورد).');
    }
}