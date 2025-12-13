<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipping','delivered','completed','cancelled','returned') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending'");
        });
    }
};

