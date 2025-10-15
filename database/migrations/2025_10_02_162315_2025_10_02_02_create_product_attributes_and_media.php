<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        // 1. Bảng Lookup cho Biến thể (Attributes)
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 100); 
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
        Schema::create('colors', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 50); 
            $table->string('hex_code', 7)->nullable(); 
            $table->timestamps();
        });
        Schema::create('sizes', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 30); 
            $table->timestamps();
        });
        Schema::create('textures', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 50); 
            $table->timestamps();
        });
        // 2. Quản lý Ảnh (Media)
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path', 500); 
            $table->string('mime_type', 50); 
            $table->string('alt_text', 255)->nullable(); 
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('textures');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('categories');
    }
};