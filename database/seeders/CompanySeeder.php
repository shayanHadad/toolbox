<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyAdmin;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');

        for ($i = 0; $i < 6; $i++) {
            $company = Company::create([
                'name'          => $faker->company(),
                'descriptions'  => $faker->realText(180),
                'founding_date' => $faker->dateTimeBetween('-20 years', '-1 years')->format('Y-m-d'),
            ]);

            // هر شرکت ۱ یا ۲ ادمین داره
            $adminCount = rand(1, 2);
            for ($j = 0; $j < $adminCount; $j++) {
                CompanyAdmin::create([
                    'companyID' => $company->companyID,
                ]);
            }
        }

        $this->command->info('شرکت‌ها و ادمین‌های شرکت ساخته شدند.');
    }
}
