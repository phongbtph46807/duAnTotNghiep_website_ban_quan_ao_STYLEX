<?php
// 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng Lookup cho Biến thể (Attributes)
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('colors', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('hex_code', 7)->nullable(); 
            $table->timestamps();
        });
        Schema::create('sizes', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->timestamps();
        });
        Schema::create('textures', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->timestamps();
        });

        // Quản lý Ảnh (Media)
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('mime_type');
            $table->string('alt_text')->nullable(); 
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
