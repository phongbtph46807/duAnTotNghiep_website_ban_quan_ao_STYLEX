<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE defect_assessments MODIFY COLUMN defect_level ENUM('LOW', 'MEDIUM', 'HIGH') DEFAULT 'MEDIUM'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE defect_assessments MODIFY COLUMN defect_level ENUM('LIGHT', 'MEDIUM', 'HEAVY') DEFAULT 'LIGHT'");
    }
};
