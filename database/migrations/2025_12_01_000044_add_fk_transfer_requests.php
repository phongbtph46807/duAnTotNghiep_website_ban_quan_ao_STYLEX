<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hàm up() giữ nguyên: Thêm các Khóa ngoại (FKs)
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('out_confirmed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('in_confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // KHẮC PHỤC LỖI 1091 VĨNH VIỄN bằng cách sử dụng DB::statement
        if (Schema::hasTable('transfer_requests')) {

            // Hàm trợ giúp để tạo tên Khóa ngoại mặc định của Laravel
            $getForeignKeyName = function($column) {
                return 'transfer_requests_' . $column . '_foreign';
            };

            // Danh sách các Khóa ngoại cần DROP
            $foreignKeys = [
                'from_warehouse_id', 'to_warehouse_id', 'variant_id', 'created_by',
                'out_confirmed_by', 'in_confirmed_by'
            ];

            foreach ($foreignKeys as $column) {
                $fkName = $getForeignKeyName($column);
                try {
                    // Thực hiện lệnh SQL thô để xóa Khóa ngoại
                    DB::statement('ALTER TABLE transfer_requests DROP FOREIGN KEY ' . $fkName);
                } catch (\Exception $e) {
                    // Bỏ qua lỗi 1091 (Lỗi phổ biến nhất khi DROP FK không tồn tại)
                    // Nếu lỗi là 1091, chúng ta bỏ qua và tiếp tục
                }
            }

            // Do chúng ta dùng DB::statement, không cần Schema::table bên dưới nữa
            // Tuy nhiên, nếu bạn muốn giữ cách gọi Schema, có thể thử như sau:
            /*
            Schema::table('transfer_requests', function (Blueprint $table) {
                 // Chỉ cần dropForeign nếu chắc chắn Khóa ngoại đã được tạo và chưa bị xóa.
                 // Trong trường hợp này, tốt nhất là dùng DB::statement như trên để đảm bảo.
            });
            */
        }
    }
};
