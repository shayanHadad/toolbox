<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('userID');

            $table->string('username')->unique();
            $table->string('password');

            $table->string('contact_number')->unique();

            // 0 Admin
            // 1 Customer
            // 2 Expert
            // 3 Company Admin
            $table->tinyInteger('role');

            $table->date('register_date');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('profile_picture')->nullable();

            $table->unsignedBigInteger('company_admin_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
