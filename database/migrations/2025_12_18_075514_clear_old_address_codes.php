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
        // Delete addresses with old numeric codes (cannot update because city NOT NULL)
        DB::statement("DELETE FROM addresses WHERE CAST(district AS CHAR) REGEXP '^[0-9]+$' OR CAST(city AS CHAR) REGEXP '^[0-9]+$'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore old address codes
    }
};
