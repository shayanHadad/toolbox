<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {

            $table->id('bookmarkID');

            $table->foreignId('customerID')
                ->constrained('users', 'userID')
                ->cascadeOnDelete();

            $table->foreignId('providerID')
                ->constrained('users', 'userID')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['customerID', 'providerID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
