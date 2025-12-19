<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên kho hàng (ví dụ: Kho Hà Nội)');
            $table->string('code')->unique()->comment('Mã kho hàng (ví dụ: HN)');
            $table->enum('type', ['PHYSICAL', 'VIRTUAL', 'CONSIGNMENT', 'SCRAP'])->default('PHYSICAL');
            $table->enum('operational_status', ['ACTIVE', 'INACTIVE', 'MAINTENANCE'])->default('ACTIVE')->comment('Trạng thái vận hành của kho');
            $table->string('address')->nullable()->comment('Địa chỉ chi tiết của kho');
            $table->timestamps();
            $table->index(['operational_status', 'type']);
        });
        
    }



    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
