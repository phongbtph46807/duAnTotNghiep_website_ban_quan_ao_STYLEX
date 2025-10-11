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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->unsignedBigInteger('category_id');
            $table->string('slug',255);
            $table->string('thumbnail',255)->nullable();
            $table->enum('status',['active','inactive']);
            $table->text('description')->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->string('meta_title')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
