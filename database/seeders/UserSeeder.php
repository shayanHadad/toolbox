<?php

namespace Database\Seeders;

use App\Models\CompanyAdmin;
use App\Models\User;
use Database\Seeders\Support\SeedContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class UserSeeder extends Seeder
{
    private array $usedUsernames = [];

    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');
        $hashedPassword = Hash::make('Password123');

        // --- ۱ کاربر ادمین کل (role 0)، دقیقاً یک نفر ---
        $this->makeUser($faker, $hashedPassword, 0);

        // --- ۷۰ مشتری (role 1) ---
        for ($i = 0; $i < 70; $i++) {
            $this->makeUser($faker, $hashedPassword, 1);
        }

        // --- ۴۰ اکسپرت (role 2) ---
        for ($i = 0; $i < 40; $i++) {
            $this->makeUser($faker, $hashedPassword, 2);
        }

        // --- نماینده‌های شرکت‌ها ---
        // به ازای هر شرکت، اولین ردیف company_admins به «مالک شرکت» (role=4)
        // اختصاص پیدا می‌کنه و بقیه‌ی ردیف‌ها (در صورت وجود) به «ادمین شرکت» (role=3).
        CompanyAdmin::orderBy('companyID')->orderBy('adminID')
            ->get()
            ->groupBy('companyID')
            ->each(function ($adminsOfCompany) use ($faker, $hashedPassword) {
                $adminsOfCompany->values()->each(function (CompanyAdmin $admin, int $index) use ($faker, $hashedPassword) {
                    $role = $index === 0 ? 4 : 3;
                    $this->makeUser($faker, $hashedPassword, $role, $admin->adminID);
                });
            });

        $this->command->info('کاربران ساخته شدند.');
    }

    private function makeUser($faker, string $hashedPassword, int $role, ?int $companyAdminId = null): User
    {
        return User::create([
            'username'         => $this->makeUsername($faker),
            'password'         => $hashedPassword,
            'contact_number'   => '09' . $faker->unique()->numerify('#########'),
            'role'             => $role,
            'first_name'       => $faker->firstName(),
            'last_name'        => $faker->lastName(),
            'date_of_birth'    => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'profile_picture'  => null,
            'company_admin_id' => $companyAdminId,
        ]);
    }

    /**
     * username لاتین و خواناست (مثل «reza.karimi42»)، مستقل از نام فارسی
     * نمایشی کاربر — چون طبق قانون alpha_dash نام فارسی توش جواب نمی‌ده.
     */
    private function makeUsername($faker): string
    {
        do {
            $first = $faker->randomElement(SeedContent::latinFirstNames());
            $last  = $faker->randomElement(SeedContent::latinLastNames());
            $username = $first . '.' . $last . $faker->numberBetween(1, 999);
        } while (isset($this->usedUsernames[$username]));

        $this->usedUsernames[$username] = true;

        return $username;
    }
}
