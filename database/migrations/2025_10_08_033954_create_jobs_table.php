<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ...
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index(); // Hàng đợi (ví dụ: 'emails', 'default')
            $table->longText('payload');      // Dữ liệu của Job
            $table->unsignedTinyInteger('attempts'); // Số lần thử lại
            $table->unsignedInteger('reserved_at')->nullable(); // Thời gian Job bị khóa
            $table->unsignedInteger('available_at'); // Thời gian Job có sẵn
            $table->unsignedInteger('created_at');
        });
    }
    // ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
