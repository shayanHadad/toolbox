<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {

            $table->id('messageID');

            $table->foreignId('senderID')
                ->constrained('users', 'userID');

            $table->foreignId('receiverID')
                ->constrained('users', 'userID');

            $table->text('message');

            $table->timestamp('created_at')->useCurrent();

            // -1 not sent
            // 0 unread
            // 1 read
            $table->tinyInteger('status')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
