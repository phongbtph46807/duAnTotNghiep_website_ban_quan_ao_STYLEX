<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('variant_color')->nullable()->after('product_variant_id');
            $table->string('variant_size')->nullable()->after('variant_color');
            $table->json('media')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['variant_color', 'variant_size', 'media']);
        });
    }
};

