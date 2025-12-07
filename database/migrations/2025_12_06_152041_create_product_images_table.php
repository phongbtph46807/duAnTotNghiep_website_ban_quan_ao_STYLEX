<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::create('product_images', function (Blueprint $table) {
        //     $table->id();

        //     $table->unsignedBigInteger('product_id');
        //     $table->unsignedBigInteger('variant_id')->nullable(); // nếu ảnh thuộc biến thể

        //     $table->string('image_url')->nullable();   // URL public (Cloudinary, S3…)
        //     $table->string('image_path')->nullable();  // đường dẫn local
        //     $table->string('alt_text')->nullable();    // mô tả ảnh

        //     $table->integer('sort_order')->default(0); // thứ tự ảnh
        //     $table->boolean('is_primary')->default(false); // ảnh chính
        //     $table->boolean('is_main')->default(false);    // ảnh đại diện cho sản phẩm/biến thể

        //     $table->timestamps();

        //     // Khóa ngoại
        //     $table->foreign('product_id')
        //         ->references('id')->on('products')
        //         ->onDelete('cascade');

        //     $table->foreign('variant_id')
        //         ->references('id')->on('product_variants')
        //         ->onDelete('cascade');
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('product_images');
    }
};
