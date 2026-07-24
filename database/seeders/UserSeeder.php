<?php

namespace Database\Seeders;

use App\Models\CompanyAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class UserSeeder extends Seeder
{
    private int $usernameCounter = 1;

    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');
        $hashedPassword = Hash::make('Password123');

        // --- ۱ کاربر ادمین کل (role 0) ---
        $this->makeUser($faker, $hashedPassword, 0);

        // --- ۴۰ مشتری (role 1) ---
        for ($i = 0; $i < 40; $i++) {
            $this->makeUser($faker, $hashedPassword, 1);
        }

        // --- ۱۵ اکسپرت (role 2) ---
        for ($i = 0; $i < 15; $i++) {
            $this->makeUser($faker, $hashedPassword, 2);
        }

        // --- ادمین‌های شرکت (role 3)، یکی به ازای هر ردیف company_admins ---
        CompanyAdmin::all()->each(function (CompanyAdmin $admin) use ($faker, $hashedPassword) {
            $this->makeUser($faker, $hashedPassword, 3, $admin->adminID);
        });

        $this->command->info('کاربران ساخته شدند.');
    }

    private function makeUser($faker, string $hashedPassword, int $role, ?int $companyAdminId = null): User
    {
        // username لاتین طبق قانون alpha_dash (فارسی رد میشه)
        $username = 'user' . $this->usernameCounter . '_' . strtolower($faker->lexify('????'));
        $this->usernameCounter++;

        return User::create([
            'username'         => $username,
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
}
