<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }

    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'OUT_CONFIRMED', 'QC_CHECKING', 'COMPLETED', 'CANCELLED'])->default('PENDING')->change();
        });
    }
};
