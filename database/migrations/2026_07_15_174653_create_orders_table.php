<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id('orderID');

            $table->foreignId('customerID')
                ->constrained('users', 'userID');

            $table->foreignId('providerID')
                ->nullable()
                ->constrained('users', 'userID');

            $table->foreignId('companyID')
                ->nullable()
                ->constrained('companies', 'companyID')
                ->nullOnDelete();

            $table->string('status');

            $table->text('comment')->nullable();

            $table->unsignedTinyInteger('rating')->nullable();

            $table->dateTime('order_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
