<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Populate location with default value if null
        DB::table('warehouse_stocks')
            ->whereNull('location')
            ->update(['location' => 'Kho chính']);
    }

    public function down(): void
    {
        // No rollback needed
    }
};
