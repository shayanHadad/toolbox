<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\Support\SeedContent;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');

        $customers = User::where('role', 1)->pluck('userID')->all();
        $experts   = User::where('role', 2)->with('expertDetail.category')->get();
        $companies = Company::with('categories')->get();

        if (empty($customers) || $experts->isEmpty()) {
            $this->command->warn('برای ساخت سفارش، حداقل باید مشتری و اکسپرت وجود داشته باشه.');
            return;
        }

        $detailsBank = SeedContent::orderDetails();
        $reviewBank  = SeedContent::reviewComments();

        // توزیع وضعیت‌ها (کدهای عددیِ Order::STATUS_*)، شبیه به چیزی که توی
        // یه سایت واقعی می‌بینیم: بیشترشون تمام‌شده، بعد در انتظار/در حال
        // انجام، و یه بخش کمتر رد/لغوشده.
        $statusWeights = [
            Order::STATUS_FINISHED    => 42,
            Order::STATUS_WAITING     => 16,
            Order::STATUS_IN_PROGRESS => 16,
            Order::STATUS_REJECTED    => 16,
            Order::STATUS_CANCELLED   => 10,
        ];

        for ($i = 0; $i < 300; $i++) {
            $status = $this->weightedRandom($faker, $statusWeights);

            // نصف سفارش‌ها برای یه اکسپرت، نصف دیگه برای یه شرکت
            // (دقیقاً طبق منطق OrderController: یا providerID پره یا companyID، نه هردو).
            $forCompany = $companies->isNotEmpty() && $faker->boolean(50);

            $customerId = $faker->randomElement($customers);
            $providerId = null;
            $companyId  = null;
            $categoryUrl = 'others';

            if ($forCompany) {
                $company = $companies->random();
                $companyId = $company->companyID;
                $categoryUrl = $company->categories->isNotEmpty()
                    ? $company->categories->random()->url
                    : 'others';
            } else {
                $expert = $experts->random();
                $providerId = $expert->userID;
                $categoryUrl = $expert->expertDetail?->category?->url ?? 'others';
            }

            $detailsPool = $detailsBank[$categoryUrl] ?? $detailsBank['others'];
            $details = $faker->randomElement($detailsPool);

            $orderDate = match ($status) {
                // در انتظار/در حال انجام: تاریخ در آینده، وگرنه با اولین بار
                // بازدید از داشبورد، Order::autoFinishPastOrders() خودکار
                // می‌بردتشون روی «تمام‌شده».
                Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS
                    => $faker->dateTimeBetween('+1 days', '+14 days'),
                Order::STATUS_FINISHED
                    => $faker->dateTimeBetween('-6 months', '-1 days'),
                default // rejected, cancelled
                    => $faker->dateTimeBetween('-2 months', '+10 days'),
            };

            $rating  = null;
            $comment = null;

            if ($status === Order::STATUS_FINISHED && $faker->boolean(70)) {
                // ۷۰٪ سفارش‌های تمام‌شده از قبل نظر گرفتن، بقیه منتظر نظر مشتری می‌مونن
                // (برای تست جریان «نیاز به بررسی» یعنی Order::needsReview()).
                $rating = $faker->randomElement([5, 5, 5, 4, 4, 3, 2, 1]);
                $comment = $faker->randomElement($reviewBank[$rating]);
            }

            Order::create([
                'customerID' => $customerId,
                'providerID' => $providerId,
                'companyID'  => $companyId,
                'status'     => $status,
                'details'    => $details,
                'comment'    => $comment,
                'rating'     => $rating,
                'order_date' => $orderDate->format('Y-m-d H:i:s'),
            ]);
        }

        $this->command->info('سفارش‌ها ساخته شدند.');
    }

    /**
     * انتخاب تصادفیِ وزن‌دار از یه آرایه‌ی [عدد وضعیت => وزن].
     * چون Order::STATUS_* حالا int هستن (نه رشته)، این متد هم باید int
     * برگردونه، وگرنه توی match(strict ===) با ثابت‌های Order جور درنمیاد.
     */
    private function weightedRandom($faker, array $weights): int
    {
        $total = array_sum($weights);
        $point = $faker->numberBetween(1, $total);

        foreach ($weights as $key => $weight) {
            $point -= $weight;
            if ($point <= 0) {
                return (int) $key;
            }
        }

        return (int) array_key_first($weights);
    }
}
