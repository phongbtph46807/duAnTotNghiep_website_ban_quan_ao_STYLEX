<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Đổi ENUM thành VARCHAR tạm thời để có thể update dữ liệu
        DB::statement("ALTER TABLE defect_assessments MODIFY COLUMN defect_level VARCHAR(20) DEFAULT 'MEDIUM'");
        
        // Cập nhật dữ liệu cũ
        DB::table('defect_assessments')
            ->where('defect_level', 'LIGHT')
            ->update(['defect_level' => 'LOW']);
        
        DB::table('defect_assessments')
            ->where('defect_level', 'HEAVY')
            ->update(['defect_level' => 'HIGH']);
        
        // Đổi lại thành ENUM mới sau khi đã cập nhật dữ liệu
        DB::statement("ALTER TABLE defect_assessments MODIFY COLUMN defect_level ENUM('LOW', 'MEDIUM', 'HIGH') DEFAULT 'MEDIUM'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE defect_assessments MODIFY COLUMN defect_level ENUM('LIGHT', 'MEDIUM', 'HEAVY') DEFAULT 'LIGHT'");
    }
};
