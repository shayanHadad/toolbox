<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ستون details برای نگه‌داشتن توضیحاتی که مشتری موقع ثبت سفارش
     * درباره‌ی نیازش می‌نویسه؛ این جدا از ستون comment هست، چون comment
     * برای نظر/بازخوردیه که بعد از تمام‌شدن سفارش ثبت می‌شه.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('details')->nullable()->after('companyID');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
};
