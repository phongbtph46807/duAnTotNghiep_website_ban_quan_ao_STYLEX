<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        // 1. Loyalty Tiers
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); 
            $table->decimal('min_spend_required', 10, 2)->default(0);
            $table->decimal('discount_rate', 4, 2)->default(0)->comment('Ưu đãi theo %');
            $table->timestamps();
        });
        // 2. ACL: Roles và Permissions
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); 
            $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); 
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('loyalty_tiers');
    }
};