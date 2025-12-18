<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Posts table đã có đầy đủ columns từ migration 2025_10_29_153511_create_posts_table.php
        // Không cần thêm gì
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback gì
    }
};
