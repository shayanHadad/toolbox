<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');

        $customers = User::where('role', 1)->pluck('userID');
        $experts   = User::where('role', 2)->pluck('userID');
        $companies = Company::pluck('companyID');

        if ($customers->isEmpty() || $experts->isEmpty()) {
            $this->command->warn('برای ساخت سفارش، حداقل باید مشتری و اکسپرت وجود داشته باشه.');
            return;
        }

        $statuses = ['waiting', 'in_progress', 'finished'];

        for ($i = 0; $i < 120; $i++) {
            $status = $faker->randomElement($statuses);

            Order::create([
                'customerID'  => $customers->random(),
                'providerID'  => $experts->random(),
                'companyID'   => $faker->boolean(50) && $companies->isNotEmpty() ? $companies->random() : null,
                'status'      => $status,
                'comment'     => $faker->boolean(60) ? $faker->realText(120) : null,
                'rating'      => $status === 'finished' ? $faker->numberBetween(1, 5) : null,
                'order_date'  => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        $this->command->info('سفارش‌ها ساخته شدند.');
    }
}
