<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\LoggableTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    use LoggableTrait;
    public function index(){
        try {
            //code...
            $categories = Category::with('children')->whereNull('parent_id')->get();
            $allCategories = Category::with('parent')->paginate(5);

            return view('admin.category.index', compact(['categories', 'allCategories']));
        } catch (\Exception $e) {
            return abort(404, "có gì đó không ổn");
        }
    }

    public function store(Request $request){
        try {
            //code...
            $request->validate([
                'category_name' => 'required|unique:categories,name',
                'parent_id' => 'nullable|exists:categories,id'
            ]);

            Category::create([
                'name' => $request->category_name,
                'parent_id' => $request->parent_id 

            ]);

           return response()->json([
                'success' => true,
                'msg' => 'Thêm Danh Mục Thành Công!'
           ]);
        } catch (\Exception $e) {
               $this->logError($e);
                return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
           ]);
        }
    }
}