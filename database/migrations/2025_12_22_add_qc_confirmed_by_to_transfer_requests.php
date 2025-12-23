<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transfer_requests', 'qc_confirmed_by')) {
            Schema::table('transfer_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('qc_confirmed_by')->nullable()->after('in_confirmed_by');
            });
        }
    }

    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->dropColumn('qc_confirmed_by');
        });
    }
};
