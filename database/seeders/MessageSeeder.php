<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');
        $userIds = User::pluck('userID');

        if ($userIds->count() < 2) {
            $this->command->warn('برای ساخت پیام حداقل باید ۲ کاربر وجود داشته باشه.');
            return;
        }

        // توجه: جدول messages فقط created_at داره نه updated_at، برای همین
        // مستقیم با DB::table() اینسرت می‌کنیم و از Eloquent::create() که
        // خودکار دنبال updated_at می‌گرده استفاده نمی‌کنیم.

        $rows = [];

        for ($i = 0; $i < 200; $i++) {
            $sender   = $userIds->random();
            do {
                $receiver = $userIds->random();
            } while ($receiver === $sender);

            $rows[] = [
                'senderID'   => $sender,
                'receiverID' => $receiver,
                'message'    => $faker->realText(80),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
                // -1 not sent | 0 unread | 1 read (طبق کامنت migration)
                'status'     => $faker->randomElement([-1, 0, 0, 1, 1, 1]),
            ];
        }

        DB::table('messages')->insert($rows);

        $this->command->info('پیام‌ها ساخته شدند.');
    }
}
