<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * نگاشت مقادیر متنی قدیمی به کدهای عددی جدید (باید دقیقاً با ثابت‌های
     * Order::STATUS_* در app/Models/Order.php هماهنگ بماند).
     */
    private const MAP = [
        'waiting'     => 1, // Order::STATUS_WAITING
        'in_progress' => 2, // Order::STATUS_IN_PROGRESS
        'finished'    => 3, // Order::STATUS_FINISHED
        'rejected'    => 4, // Order::STATUS_REJECTED
        'cancelled'   => 5, // Order::STATUS_CANCELLED
    ];

    public function up(): void
    {
        // ۱. تبدیل مقادیر متنی شناخته‌شده به رشته‌های عددی، وقتی ستون هنوز VARCHAR است
        foreach (self::MAP as $text => $numeric) {
            DB::table('orders')->where('status', $text)->update(['status' => (string) $numeric]);
        }

        // ۲. هر مقدار دیگری که عددی نیست (مثلاً داده‌ی قدیمیِ ناشناخته، فاصله‌ی
        // اضافی، حروف بزرگ/کوچک متفاوت، یا NULL) رو قبل از تغییر نوع ستون،
        // با مقدار پیش‌فرض «در انتظار» (۱) جایگزین می‌کنیم. بدون این قدم،
        // اگه حتی یک ردیفِ نامنطبق باقی بمونه، ALTER زیر یا مستقیماً خطا
        // می‌ده یا مقداری رو silently truncate می‌کنه که بعداً توی مقایسه‌های
        // عددی (مثل Order::autoFinishPastOrders) باعث خطای
        // "Truncated incorrect DECIMAL value" می‌شه.
        DB::statement("UPDATE orders SET status = '1' WHERE status IS NULL OR status NOT REGEXP '^[0-9]+$'");

        // ۳. تغییر نوع ستون از VARCHAR به عدد صحیح کوچک (بدون نیاز به doctrine/dbal)
        DB::statement('ALTER TABLE orders MODIFY status TINYINT UNSIGNED NOT NULL DEFAULT 1');
    }

    public function down(): void
    {
        // برگرداندن نوع ستون به رشته
        DB::statement("ALTER TABLE orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'waiting'");

        // برگرداندن مقادیر عددی به معادل متنی قبلی
        foreach (self::MAP as $text => $numeric) {
            DB::table('orders')->where('status', (string) $numeric)->update(['status' => $text]);
        }
    }
};
