<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyBookmark;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanyBookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 1)->pluck('userID');
        $companies = Company::pluck('companyID');

        if ($customers->isEmpty() || $companies->isEmpty()) {
            $this->command->warn('برای ساخت بوکمارک شرکت حداقل باید مشتری و شرکت وجود داشته باشه.');
            return;
        }

        $used = [];
        $created = 0;
        $attempts = 0;

        while ($created < 50 && $attempts < 1000) {
            $attempts++;

            $customerId = $customers->random();
            $companyId  = $companies->random();
            $key = $customerId . '-' . $companyId;

            if (isset($used[$key])) {
                continue;
            }

            $used[$key] = true;

            CompanyBookmark::create([
                'customerID' => $customerId,
                'companyID'  => $companyId,
            ]);

            $created++;
        }

        $this->command->info("بوکمارک‌های شرکت ساخته شدند ({$created} عدد).");
    }
}
