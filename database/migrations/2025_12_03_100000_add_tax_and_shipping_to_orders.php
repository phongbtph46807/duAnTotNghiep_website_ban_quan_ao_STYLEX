<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thuế
            $table->unsignedBigInteger('tax_rate_id')->nullable()->after('discount');
            $table->unsignedBigInteger('tax_amount')->default(0)->after('tax_rate_id');

            // Đơn vị vận chuyển (lưu lại hãng ship đã chọn)
            $table->unsignedBigInteger('shipping_carrier_id')->nullable()->after('shipping_fee');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->nullOnDelete();
            $table->foreign('shipping_carrier_id')->references('id')->on('shipping_carriers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tax_rate_id')) {
                $table->dropForeign(['tax_rate_id']);
                $table->dropColumn(['tax_rate_id', 'tax_amount']);
            }
            if (Schema::hasColumn('orders', 'shipping_carrier_id')) {
                $table->dropForeign(['shipping_carrier_id']);
                $table->dropColumn('shipping_carrier_id');
            }
        });
    }
};


