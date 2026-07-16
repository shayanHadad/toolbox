<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expert_details', function (Blueprint $table) {

            $table->unsignedBigInteger('userID')->primary();

            $table->foreign('userID')
                ->references('userID')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreignId('categoryID')
                ->constrained('work_categories', 'categoryID');

            $table->decimal('rating_avg', 3, 2)->default(0);

            $table->text('description')->nullable();

            $table->text('resume')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_details');
    }
};
