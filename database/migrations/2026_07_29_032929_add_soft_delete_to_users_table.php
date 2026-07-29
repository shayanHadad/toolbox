<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * با اضافه‌شدن deleted_at، حذف کاربر (User::delete()) دیگه یک
     * DELETE واقعی نمی‌زنه، فقط این ستون رو پر می‌کنه. چون ردیف فیزیکاً
     * سرجاشه، FK هایی که به userID اشاره دارن (messages.senderID/
     * receiverID، orders.customerID/providerID) هیچ‌وقت درگیر نمی‌شن؛
     * دیگه لازم نیست قبل از حذف کاربر، پیام‌ها/سفارش‌هاش رو دستی پاک یا
     * جابه‌جا کنیم.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
