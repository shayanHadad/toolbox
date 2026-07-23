<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expert_details', function (Blueprint $table) {
            $table->dropColumn('rating_avg');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_details', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->nullable()->after('categoryID');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->nullable()->after('work_category');
        });
    }
};
