<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Texture;
use Illuminate\Support\Facades\DB;

class TextureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed
        // Sử dụng DB::table để tránh lỗi foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Texture::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Danh sách các chất liệu phổ biến
        $textures = [
            ['name' => 'Cotton', 'description' => 'Vải cotton mềm mại, thấm hút tốt', 'status' => 1],
            ['name' => 'Polyester', 'description' => 'Vải polyester bền, không nhăn', 'status' => 1],
            ['name' => 'Vải lanh', 'description' => 'Vải lanh mát mẻ, thoáng khí', 'status' => 1],
            ['name' => 'Vải lụa', 'description' => 'Vải lụa mềm mại, sang trọng', 'status' => 1],
            ['name' => 'Denim', 'description' => 'Vải denim bền chắc, phong cách', 'status' => 1],
            ['name' => 'Vải kaki', 'description' => 'Vải kaki chắc chắn, lịch sự', 'status' => 1],
            ['name' => 'Vải len', 'description' => 'Vải len ấm áp, mềm mại', 'status' => 1],
            ['name' => 'Vải thun', 'description' => 'Vải thun co giãn tốt, thoải mái', 'status' => 1],
            ['name' => 'Vải voan', 'description' => 'Vải voan mỏng, nhẹ nhàng', 'status' => 1],
            ['name' => 'Vải satin', 'description' => 'Vải satin bóng, sang trọng', 'status' => 1],
            ['name' => 'Vải jean', 'description' => 'Vải jean cổ điển, bền chắc', 'status' => 1],
            ['name' => 'Vải dù', 'description' => 'Vải dù chống nước, bền', 'status' => 1],
            ['name' => 'Vải nỉ', 'description' => 'Vải nỉ ấm áp, mềm mại', 'status' => 1],
            ['name' => 'Vải lụa tơ tằm', 'description' => 'Vải lụa tơ tằm cao cấp, mềm mại', 'status' => 1],
            ['name' => 'Vải canvas', 'description' => 'Vải canvas chắc chắn, bền', 'status' => 1],
        ];

        foreach ($textures as $texture) {
            Texture::create($texture);
        }
    }
}
