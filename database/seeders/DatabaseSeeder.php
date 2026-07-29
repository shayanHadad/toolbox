<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WorkCategorySeeder::class, // دسته‌بندی‌ها (دیتای دستی خودت)
            CompanySeeder::class,        // companies + company_admins
            UserSeeder::class,           // admin + customers + experts + company-admin users
            ExpertDetailSeeder::class,   // نیاز به اکسپرت‌ها و دسته‌بندی‌های موجود
            CompanyCategorySeeder::class,// نیاز به شرکت‌ها و دسته‌بندی‌های موجود
            OrderSeeder::class,          // نیاز به مشتری‌ها و اکسپرت‌ها
            MessageSeeder::class,        // نیاز به کاربران و سفارش‌ها
            BookmarkSeeder::class,       // نیاز به مشتری‌ها و اکسپرت‌ها
            CompanyBookmarkSeeder::class,// نیاز به مشتری‌ها و شرکت‌ها
        ]);
    }
}