<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MAP = [
        'waiting'     => 1, // Order::STATUS_WAITING
        'in_progress' => 2, // Order::STATUS_IN_PROGRESS
        'finished'    => 3, // Order::STATUS_FINISHED
        'rejected'    => 4, // Order::STATUS_REJECTED
        'cancelled'   => 5, // Order::STATUS_CANCELLED
    ];

    public function up(): void
    {
        foreach (self::MAP as $text => $numeric) {
            DB::table('orders')->where('status', $text)->update(['status' => (string) $numeric]);
        }

        DB::statement("UPDATE orders SET status = '1' WHERE status IS NULL OR status NOT REGEXP '^[0-9]+$'");

        DB::statement('ALTER TABLE orders MODIFY status TINYINT UNSIGNED NOT NULL DEFAULT 1');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'waiting'");

        foreach (self::MAP as $text => $numeric) {
            DB::table('orders')->where('status', (string) $numeric)->update(['status' => $text]);
        }
    }
};
