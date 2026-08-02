<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WorkCategorySeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            ExpertDetailSeeder::class,
            CompanyCategorySeeder::class,
            OrderSeeder::class,
            MessageSeeder::class,
            BookmarkSeeder::class,
            CompanyBookmarkSeeder::class,
        ]);
    }
}
