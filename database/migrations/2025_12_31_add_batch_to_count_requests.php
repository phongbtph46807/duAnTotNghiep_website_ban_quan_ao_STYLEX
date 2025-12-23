<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('count_requests', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('variant_id')->comment('Mã lô hàng');
            $table->string('location')->nullable()->after('batch_number')->comment('Vị trí lô');
        });
    }

    public function down(): void
    {
        Schema::table('count_requests', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'location']);
        });
    }
};
