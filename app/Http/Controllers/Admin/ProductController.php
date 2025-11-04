<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Texture;
use App\Models\User;
use App\Traits\LoggableTrait;
use App\Traits\UploadToLocalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use LoggableTrait, UploadToLocalTrait;

    const FOLDER = 'products';
    public function index(Request $request)
    {
        $queryproducts = Product::query()->latest('id');

        if ($request->filled('name')) {
            $queryproducts->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('category_id')) {
            $queryproducts->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $queryproducts->where('status', $request->status);
        }
        if ($request->filled('is_featured')) {
            $queryproducts->where('is_featured', $request->is_featured);
        }

        $queryproductCounts =
            Product::query()
            ->selectRaw('
                    count(id) as total_products,
                    sum(is_active = "1") as active_products,
                    sum(is_active = "0") as inactive_products,
                    sum(is_featured = 1) as featured_products
                ');
        $items = $queryproducts->paginate(10);
        $productCounts = $queryproductCounts->first();
        $categories = Category::all();
        return view('admin.products.index', compact('items', 'productCounts', 'categories'));
    }
    public function create()
    {
        $categories = Category::query()->where('status', 1)->get();
        $colors = Color::query()->where('status', 1)->get();
        $textures = Texture::query()->where('status', 1)->get();
        $sizes = Size::query()->where('status', 1)->get();
        return view('admin.products.create', compact('categories', 'colors', 'textures', 'sizes'));
    }
    public function store(ProductRequest $request)
    {

        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();
                if (!empty($data['thumbnail'])) {
                    $urlThumbnail = Storage::put(self::FOLDER, $data['thumbnail']);
                } else {
                    $urlThumbnail = null;
                }
                $data['thumbnail'] = $urlThumbnail;

                $product = Product::query()->create($data);

             
            });
            return redirect()->route('admin.products.index')->with('success', 'Thêm mới thành công');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }

        // try {
        //     DB::beginTransaction();

        //     $data = $request->except('thumbnail');

        //     if ($request->hasFile('thumbnail')) {
        //         $urlThumbnail = $this->uploadToLocal($request->file('thumbnail'), self::FOLDER);
        //         $data['thumbnail'] = $urlThumbnail;
        //     }

        //     $data['slug'] = Str::slug($data['name']);
        //     // dd($data);
        //     $product = Product::query()->create($data);

        //     DB::commit();

        //     return redirect()->route('admin.products.index')->with('success', 'Thêm mới thành công');
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     if (isset($urlThumbnail) && filter_var($urlThumbnail, FILTER_VALIDATE_URL)) {
        //         $this->deleteFromLocal($urlThumbnail, self::FOLDER);
        //     }

        //     $this->logError($e);

        //     return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        // }
    }
    public function toggleFeature(Request $request, Product $product)
    {
        $product->is_featured = $request->input('is_featured') ? 1 : 0;
        $product->save();

        return response()->json(['message' => 'Cập nhật trạng thái sản phẩm nổi bật thành công.']);
    }
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }
    public function edit(Product $product)
    {
        $categories = Category::query()->where('status', 1)->get();
        $colors = Color::query()->where('status', 1)->get();
        $textures = Texture::query()->where('status', 1)->get();
        $sizes = Size::query()->where('status', 1)->get();
        return view('admin.products.edit', compact('categories', 'product', 'colors', 'textures', 'sizes'));
    }
    public function update(ProductRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $data = $request->validated();

                // 1) Thumbnail: nếu có upload mới thì thay, không thì giữ nguyên
                if (!empty($data['thumbnail'])) {
                    if ($product->thumbnail) {
                        Storage::delete($product->thumbnail);
                    }
                    $data['thumbnail'] = Storage::put(self::FOLDER, $data['thumbnail']);
                } else {
                    unset($data['thumbnail']);
                }

                // 2) Update product
                $product->update($data);

                // 3) Update/Create variants (KHÔNG xoá biến thể cũ)
               

                // 4) KHÔNG xoá biến thể trong update theo yêu cầu
            });

            return redirect()->route('admin.products.index')->with('success', 'Cập nhật thành công');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Đã chuyển vào thùng rác!');
        } catch (\Exception $e) {
            $this->logError($e);

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }

    public function trash()
    {
        try {
            $productsDeleted = Product::onlyTrashed()->latest('id')->paginate(10);
            $categories = Category::all();
            return view('admin.products.trash', compact('productsDeleted', 'categories'));
        } catch (\Exception $e) {
            $this->logError($e);

            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }
    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();
            return redirect()->route('admin.products.trash')->with('success', 'Khôi phục sản phẩm thành công');
        } catch (\Exception $e) {
            $this->logError($e);

            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }
    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->forceDelete();
            return redirect()->route('admin.products.trash')->with('success', 'Xóa cứng người dùng thành công');
        } catch (\Exception $e) {
            $this->logError($e);

            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }
}
