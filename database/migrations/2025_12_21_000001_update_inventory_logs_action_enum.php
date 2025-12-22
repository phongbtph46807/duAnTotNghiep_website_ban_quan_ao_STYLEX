<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory_logs MODIFY action ENUM('IN', 'OUT', 'RESERVE', 'RELEASE', 'TRANSFER', 'ADJUSTMENT') DEFAULT 'IN'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_logs MODIFY action ENUM('IN', 'OUT', 'RESERVE', 'TRANSFER', 'ADJUSTMENT') DEFAULT 'IN'");
    }
};
