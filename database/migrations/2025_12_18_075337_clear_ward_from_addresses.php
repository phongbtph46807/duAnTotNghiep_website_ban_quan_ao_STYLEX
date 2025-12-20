<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear old ward data (quận/huyện mã codes)
        DB::table('addresses')->update(['ward' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore old ward data
    }
};
