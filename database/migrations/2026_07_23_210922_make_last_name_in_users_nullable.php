<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('register_date');

            $table->string('last_name', 100)->nullable()->change();
        });
    }

    public function down(): void {}
};
