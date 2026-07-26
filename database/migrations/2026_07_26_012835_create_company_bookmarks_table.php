<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_bookmarks', function (Blueprint $table) {

            $table->id('companyBookmarkID');

            $table->foreignId('customerID')
                ->constrained('users', 'userID')
                ->cascadeOnDelete();

            $table->foreignId('companyID')
                ->constrained('companies', 'companyID')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['customerID', 'companyID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_bookmarks');
    }
};
