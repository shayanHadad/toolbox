<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * توجه: نوع companyID را unsignedBigInteger فرض کرده‌ایم چون معمولاً
     * ستون‌های primary-key در Laravel از این نوع هستند. اگر ستون
     * companies.companyID از نوع دیگری (مثلاً unsignedInteger) است،
     * این migration را متناسب با آن اصلاح کنید وگرنه در ایجاد
     * foreign key با خطا مواجه می‌شوید.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('companyID')->nullable()->after('receiverID');

            $table->foreign('companyID')
                ->references('companyID')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['companyID']);
            $table->dropColumn('companyID');
        });
    }
};
