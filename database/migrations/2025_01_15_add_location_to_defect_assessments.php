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
                // Thêm location sau variant_id (không dùng after batch_number vì có thể chưa tồn tại)
                if (Schema::hasColumn('defect_assessments', 'batch_number')) {
                    $table->string('location')->nullable()->after('batch_number');
                } else {
                    $table->string('location')->nullable()->after('variant_id');
                }
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
