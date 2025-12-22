<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('transfer_requests', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('transfer_requests', 'location')) {
                $table->string('location')->nullable()->after('batch_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            if (Schema::hasColumn('transfer_requests', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
