<?php

namespace Database\Seeders;

use App\Models\ExpertDetail;
use App\Models\User;
use App\Models\WorkCategory;
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

        User::where('role', 2)->get()->each(function (User $expert) use ($categories, $faker) {
            ExpertDetail::create([
                'userID'      => $expert->userID,
                'categoryID'  => $categories->random()->categoryID,
                'description' => $faker->realText(150),
                'resume'      => $faker->realText(400),
            ]);
        });

        $this->command->info('جزئیات اکسپرت‌ها ساخته شد.');
    }
}
