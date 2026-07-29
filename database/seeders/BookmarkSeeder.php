<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 1)->pluck('userID');
        $experts   = User::where('role', 2)->pluck('userID');

        if ($customers->isEmpty() || $experts->isEmpty()) {
            $this->command->warn('برای ساخت بوکمارک حداقل باید مشتری و اکسپرت وجود داشته باشه.');
            return;
        }

        $used = [];
        $created = 0;
        $attempts = 0;

        while ($created < 70 && $attempts < 1000) {
            $attempts++;

            $customerId = $customers->random();
            $providerId = $experts->random();
            $key = $customerId . '-' . $providerId;

            if (isset($used[$key])) {
                continue;
            }

            $used[$key] = true;

            Bookmark::create([
                'customerID' => $customerId,
                'providerID' => $providerId,
            ]);

            $created++;
        }

        $this->command->info("بوکمارک‌ها ساخته شدند ({$created} عدد).");
    }
}
