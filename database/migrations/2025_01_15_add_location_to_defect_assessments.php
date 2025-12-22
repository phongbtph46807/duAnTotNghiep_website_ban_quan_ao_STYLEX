<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defect_assessments') && !Schema::hasColumn('defect_assessments', 'location')) {
            Schema::table('defect_assessments', function (Blueprint $table) {
                $table->string('location')->nullable()->after('batch_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('defect_assessments') && Schema::hasColumn('defect_assessments', 'location')) {
            Schema::table('defect_assessments', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }
    }
};
