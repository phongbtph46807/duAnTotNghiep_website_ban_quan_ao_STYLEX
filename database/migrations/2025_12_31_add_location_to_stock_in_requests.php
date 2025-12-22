<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_in_requests', function (Blueprint $table) {
            $table->string('location')->nullable()->after('batch_number')->comment('Vị trí lô (kệ, tầng, vùng)');
        });
    }

    public function down(): void
    {
        Schema::table('stock_in_requests', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
