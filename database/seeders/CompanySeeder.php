<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyAdmin;
use Database\Seeders\Support\SeedContent;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');

        foreach (SeedContent::companies() as $data) {
            $company = Company::create([
                'name'          => $data['name'],
                'descriptions'  => $data['descriptions'],
                'founding_date' => $faker->dateTimeBetween('-20 years', '-1 years')->format('Y-m-d'),
            ]);

            // هر شرکت حتماً یک ردیف company_admins برای مالک (role=4) داره.
            // ترتیب ساخت مهمه: اولین ردیفِ هر شرکت توسط UserSeeder به‌عنوان
            // «مالک» در نظر گرفته می‌شه.
            CompanyAdmin::create(['companyID' => $company->companyID]);

            // بیشتر شرکت‌ها حداقل یک ادمین اضافه هم دارن؛ بعضی‌ها حتی دوتا،
            // شبیه یه تیم واقعی که چندنفره پاسخگوی مشتری‌هاست.
            $extraAdmins = $faker->randomElement([0, 1, 1, 1, 2]);

            for ($i = 0; $i < $extraAdmins; $i++) {
                CompanyAdmin::create(['companyID' => $company->companyID]);
            }
        }

        $this->command->info('شرکت‌ها و ردیف‌های ادمین شرکت ساخته شدند.');
    }
}
