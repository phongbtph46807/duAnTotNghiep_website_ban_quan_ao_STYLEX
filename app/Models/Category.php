<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'parent_id',
        'status'
    ];

    public function parent(){
        return $this->belongsTo(Category::class,'parent_id');
    }
    public function children(){
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function getAllDescendantIdsAndSelf()
    {
        // Lấy tất cả con cháu chỉ bằng 1 query (nhờ 'childrenRecursive' đã Eager Load)
        $descendants = collect();
        $children = $this->childrenRecursive; // Lấy từ quan hệ đã load

        // Hàm (Closure) đệ quy để gom ID
        $collectIds = function ($categories) use (&$descendants, &$collectIds) {
            foreach ($categories as $category) {
                $descendants->push($category->id);
                if ($category->childrenRecursive->isNotEmpty()) {
                    // Đệ quy tiếp nếu vẫn còn con
                    $collectIds($category->childrenRecursive);
                }
            }
        };

        // Bắt đầu gom ID từ các con cấp 1
        $collectIds($children);

        // Thêm ID của chính nó (danh mục cha) vào danh sách
        $descendants->push($this->id); 
        
        return $descendants->unique(); // Trả về danh sách ID [id_cha, id_con1, id_con2, id_chau1...]
    }
}
