<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_categories', function (Blueprint $table) {

            $table->foreignId('companyID')
                ->constrained('companies', 'companyID')
                ->cascadeOnDelete();

            $table->foreignId('categoryID')
                ->constrained('work_categories', 'categoryID')
                ->cascadeOnDelete();

            $table->primary(['companyID', 'categoryID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_categories');
    }
};
