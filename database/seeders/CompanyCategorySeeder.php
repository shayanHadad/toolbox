<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\WorkCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = WorkCategory::all();

        if ($categories->isEmpty()) {
            $this->command->warn('جدول work_categories خالیه — company_categories ساخته نشد.');
            return;
        }

        Company::all()->each(function (Company $company) use ($categories) {
            $picked = $categories->random(min(rand(1, 3), $categories->count()));

            foreach ($picked as $category) {
                DB::table('company_categories')->insertOrIgnore([
                    'companyID'  => $company->companyID,
                    'categoryID' => $category->categoryID,
                ]);
            }
        });

        $this->command->info('دسته‌بندی‌های شرکت‌ها ساخته شد.');
    }
}
