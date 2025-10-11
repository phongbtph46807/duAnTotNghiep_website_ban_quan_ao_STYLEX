<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_admin')->default(0);
            $table->text('verification_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->tinyInteger('is_verified')->default(0); 
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('hire_date')->nullable();
            $table->softDeletes(); // tạo trường deleted_at
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
