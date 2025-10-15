<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        // 1. Thuế (Tax Rates)
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('rate', 5, 4); 
            $table->timestamps();
        });
        // 2. Vận chuyển
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 50); 
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('shipping_carriers');
        Schema::dropIfExists('tax_rates');
    }
};