<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_name')->nullable()->after('code');
            $table->string('buyer_phone', 30)->nullable()->after('buyer_name');
            $table->string('buyer_email')->nullable()->after('buyer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_phone', 'buyer_email']);
        });
    }
};

