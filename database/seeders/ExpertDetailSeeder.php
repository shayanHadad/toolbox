<?php

namespace Database\Seeders;

use App\Models\ExpertDetail;
use App\Models\User;
use App\Models\WorkCategory;
use Database\Seeders\Support\SeedContent;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class ExpertDetailSeeder extends Seeder
{
    public function run(): void
    {
        $categories = WorkCategory::all();

        if ($categories->isEmpty()) {
            $this->command->warn('جدول work_categories خالیه — expert_details ساخته نشد. اول چندتا دسته‌بندی اضافه کن.');
            return;
        }

        $faker = FakerFactory::create('fa_IR');
        $bios    = SeedContent::expertBios();
        $resumes = SeedContent::expertResumes();

        User::where('role', 2)->get()->each(function (User $expert) use ($categories, $faker, $bios, $resumes) {
            $category = $categories->random();
            $url = $category->url;

            $bioPool    = $bios[$url]    ?? $bios['others'];
            $resumePool = $resumes[$url] ?? $resumes['others'];

            ExpertDetail::create([
                'userID'      => $expert->userID,
                'categoryID'  => $category->categoryID,
                'description' => $faker->randomElement($bioPool),
                'resume'      => $faker->randomElement($resumePool),
            ]);
        });

        $this->command->info('جزئیات اکسپرت‌ها ساخته شد.');
    }
}
