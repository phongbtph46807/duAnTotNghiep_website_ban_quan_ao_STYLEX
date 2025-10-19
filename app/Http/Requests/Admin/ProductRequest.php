<?php

// namespace App\Http\Requests\Admin;

// use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rule;
// use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\DB;

// class ProductRequest extends FormRequest
// {
//     public function authorize(): bool
//     {
//         return true;
//     }

//     protected function prepareForValidation(): void
//     {
//         $price      = preg_replace('/\./', '', (string) $this->input('price', '0'));
//         $priceSale  = preg_replace('/\./', '', (string) $this->input('price_sale', '0'));

//         $variants = $this->input('variants', []);
//         if (is_array($variants)) {
//             foreach ($variants as $i => $row) {
//                 if (isset($row['price'])) {
//                     $variants[$i]['price'] = (int) preg_replace('/\./', '', (string) $row['price']);
//                 }
//                 // Chuẩn hoá status checkbox (có thể gửi 0/1 hoặc “on”)
//                 if (isset($row['status'])) {
//                     $variants[$i]['status'] = (int) (!!$row['status']);
//                 }
//             }
//         }

//         $this->merge([
//             'price'       => (int) $price,
//             'price_sale'  => (int) $priceSale,
//             'is_active'   => (int) (!!$this->input('is_active', 0)),
//             'is_featured' => (int) (!!$this->input('is_featured', 0)),
//             'variants'    => $variants,
//         ]);
//     }

//     public function rules(): array
//     {
//         if ($this->isMethod('post')) {
//             return $this->rulesForCreate();
//         }

//         if ($this->isMethod('put') || $this->isMethod('patch')) {
//             return $this->rulesForUpdate();
//         }

//         return [];
//     }

//     public function rulesForCreate(): array
//     {
//         return array_merge($this->baseProductRules(), $this->variantRules(false));
//     }

//     public function rulesForUpdate(): array
//     {
//         $productId = optional($this->route('product'))->id; // route model binding: admin.products.update

//         return array_merge($this->baseProductRules($productId), $this->variantRules(true));
//     }

//     protected function baseProductRules(?int $productId = null): array
//     {
//         $uniqueName = Rule::unique('products', 'name')->whereNull('deleted_at');
//         $uniqueSlug = Rule::unique('products', 'slug')->whereNull('deleted_at');

//         if ($productId) {
//             $uniqueName = $uniqueName->ignore($productId);
//             $uniqueSlug = $uniqueSlug->ignore($productId);
//         }

//         return [
//             'name'        => ['required', 'string', 'max:255', $uniqueName],
//             'slug'        => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $uniqueSlug],
//             'category_id' => ['required', 'integer', 'exists:categories,id'],
//             'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
//             'meta_title'  => ['nullable', 'string', 'max:255'],
//             'description' => ['nullable', 'string'],
//             'is_active'   => ['required', 'in:0,1'],
//             'is_featured' => ['required', 'in:0,1'],
//             'price'       => ['required', 'integer', 'min:0'],
//             'price_sale'  => ['nullable', 'integer', 'min:0', 'lte:price'],
//         ];
//     }


//     protected function variantRules(bool $isUpdate): array
//     {
//         $rules = [
//             'variants'                 => ['sometimes', 'array'],
//             'variants.*.color_id'      => ['required_with:variants', 'nullable', 'integer', 'exists:colors,id'],
//             'variants.*.size_id'       => ['required_with:variants', 'nullable', 'integer', 'exists:sizes,id'],
//             'variants.*.texture_id'    => ['required_with:variants', 'nullable', 'integer', 'exists:textures,id'],
//             'variants.*.price'         => ['required_with:variants', 'integer', 'min:0'],
//             'variants.*.quantity'        => ['nullable', 'integer', 'min:1'],
//             'variants.*.image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
//             'variants.*.status'        => ['nullable', 'in:0,1'],
//         ];

//         // Khi cập nhật, nếu gửi kèm id của variant, cho phép unique SKU bỏ qua chính nó
//         if ($isUpdate) {
//             $rules['variants.*.id'] = ['sometimes', 'integer', 'exists:product_variants,id'];
//             $rules['variants.*.sku'] = [
//                 'required_with:variants',
//                 'string',
//                 'max:255',
//                 'distinct',
//                 Rule::unique('product_variants', 'sku')
//                     ->whereNull('deleted_at')
//                     ->ignore(fn() => (int) ($this->input('variants.*.id') ?? 0)), // sẽ được xử lý trong withValidator()
//             ];
//         }

//         return $rules;
//     }


//     public function withValidator($validator)
//     {
//         $validator->after(function ($v) {
//             $variants = $this->input('variants', []);
//             if (!is_array($variants) || empty($variants)) {
//                 return;
//             }

//             // 1) Nếu có bất kỳ biến thể => bắt buộc mỗi dòng đủ 3 thuộc tính
//             foreach ($variants as $idx => $row) {
//                 $c = Arr::get($row, 'color_id');
//                 $s = Arr::get($row, 'size_id');
//                 $t = Arr::get($row, 'texture_id');

//                 if (!$c || !$s || !$t) {
//                     $v->errors()->add("variants.$idx", 'Mỗi biến thể phải chọn đủ Màu sắc, Kích cỡ và Chất liệu.');
//                 }
//             }

//             // 2) Chặn trùng tổ hợp trong request
//             $seen = [];
//             foreach ($variants as $idx => $row) {
//                 $key = implode(':', [
//                     Arr::get($row, 'color_id') ?: '0',
//                     Arr::get($row, 'size_id') ?: '0',
//                     Arr::get($row, 'texture_id') ?: '0',
//                 ]);

//                 if (isset($seen[$key])) {
//                     $v->errors()->add("variants.$idx", 'Tổ hợp Màu/Size/Chất liệu bị trùng với dòng khác.');
//                 } else {
//                     $seen[$key] = true;
//                 }
//             }

//             // 3) Khi update: xử lý unique sku bỏ qua chính variant.*.id (nếu có)
//             if ($this->isMethod('put') || $this->isMethod('patch')) {
//                 foreach ($variants as $idx => $row) {
//                     if (!isset($row['sku'])) continue;

//                     $ignoreId = (int) ($row['id'] ?? 0);
//                     if ($ignoreId > 0) {
//                         $exists = DB::table('product_variants')
//                             ->whereNull('deleted_at')
//                             ->where('sku', $row['sku'])
//                             ->where('id', '!=', $ignoreId)
//                             ->exists();

//                         if ($exists) {
//                             $v->errors()->add("variants.$idx.sku", 'SKU đã tồn tại.');
//                         }
//                     }
//                 }
//             }
//         });
//     }

//     public function messages(): array
//     {
//         return [
//             'name.required'        => 'Tên sản phẩm là bắt buộc.',
//             'name.max'             => 'Tên sản phẩm tối đa :max ký tự.',
//             'name.unique'          => 'Tên sản phẩm đã tồn tại.',
//             'slug.required'        => 'Slug là bắt buộc.',
//             'slug.regex'           => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
//             'slug.unique'          => 'Slug đã tồn tại.',
//             'category_id.required' => 'Vui lòng chọn danh mục.',
//             'category_id.exists'   => 'Danh mục không hợp lệ.',
//             'thumbnail.image'      => 'Ảnh đại diện phải là định dạng ảnh.',
//             'thumbnail.mimes'      => 'Ảnh đại diện chỉ chấp nhận jpg, jpeg, png, webp, gif.',
//             'thumbnail.max'        => 'Ảnh đại diện tối đa 5MB.',
//             'price.required'       => 'Giá gốc là bắt buộc.',
//             'price.integer'        => 'Giá gốc phải là số.',
//             'price.min'            => 'Giá gốc không được âm.',
//             'price_sale.integer'   => 'Giá khuyến mãi phải là số.',
//             'price_sale.lte'       => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
//             'is_active.in'         => 'Trạng thái không hợp lệ.',
//             'is_featured.in'       => 'Cờ nổi bật không hợp lệ.',

//             'variants.array'                   => 'Dữ liệu biến thể không hợp lệ.',
//             'variants.*.color_id.required_with' => 'Thiếu màu sắc cho biến thể.',
//             'variants.*.size_id.required_with' => 'Thiếu kích cỡ cho biến thể.',
//             'variants.*.texture_id.required_with' => 'Thiếu chất liệu cho biến thể.',

//             'variants.*.sku.required_with'     => 'SKU là bắt buộc.',
//             'variants.*.sku.max'               => 'SKU tối đa :max ký tự.',
//             'variants.*.sku.distinct'          => 'SKU các biến thể không được trùng nhau.',
//             'variants.*.sku.unique'            => 'SKU đã tồn tại trong hệ thống.',

//             'variants.*.price.required_with'   => 'Giá của biến thể là bắt buộc.',
//             'variants.*.price.integer'         => 'Giá của biến thể phải là số nguyên.',
//             'variants.*.price.min'             => 'Giá của biến thể không được âm.',
//             'variants.*.weight.numeric'        => 'Khối lượng phải là số.',
//             'variants.*.image.image'           => 'Ảnh biến thể phải là định dạng ảnh.',
//             'variants.*.image.mimes'           => 'Ảnh biến thể chỉ chấp nhận jpg, jpeg, png, webp.',
//             'variants.*.image.max'             => 'Ảnh biến thể tối đa 5MB.',
//             'variants.*.status.in'             => 'Trạng thái biến thể không hợp lệ.',
//         ];
//     }

//     public function attributes(): array
//     {
//         return [
//             'name'        => 'tên sản phẩm',
//             'slug'        => 'slug',
//             'category_id' => 'danh mục',
//             'thumbnail'   => 'ảnh đại diện',
//             'meta_title'  => 'tiêu đề SEO',
//             'description' => 'mô tả',
//             'price'       => 'giá gốc',
//             'price_sale'  => 'giá khuyến mãi',

//             'variants'                 => 'biến thể',
//             'variants.*.color_id'      => 'màu sắc',
//             'variants.*.size_id'       => 'kích cỡ',
//             'variants.*.texture_id'    => 'chất liệu',
//             'variants.*.sku'           => 'SKU',
//             'variants.*.price'         => 'giá biến thể',
//             'variants.*.weight'        => 'khối lượng',
//             'variants.*.image'         => 'ảnh biến thể',
//             'variants.*.status'        => 'trạng thái biến thể',
//         ];
//     }
// }


namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $price     = preg_replace('/\./', '', (string) $this->input('price', '0'));
        $priceSale = preg_replace('/\./', '', (string) $this->input('price_sale', '0'));

        $variants = $this->input('variants', []);
        if (is_array($variants)) {
            foreach ($variants as $i => $row) {
                if (isset($row['price'])) {
                    $variants[$i]['price'] = (int) preg_replace('/\./', '', (string) $row['price']);
                }
                // quantity mặc định >=1
                if (!isset($row['quantity']) || $row['quantity'] === '') {
                    $variants[$i]['quantity'] = 1;
                } else {
                    $variants[$i]['quantity'] = (int) $row['quantity'];
                }
                // Chuẩn hoá status checkbox
                $variants[$i]['status'] = (int) (!!($row['status'] ?? 0));
            }
        }

        $this->merge([
            'price'       => (int) $price,
            'price_sale'  => (int) $priceSale,
            'is_active'   => (int) (!!$this->input('is_active', 0)),
            'is_featured' => (int) (!!$this->input('is_featured', 0)),
            'variants'    => $variants,
        ]);
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->rulesForCreate();
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->rulesForUpdate();
        }

        return [];
    }

    public function rulesForCreate(): array
    {
        return array_merge($this->baseProductRules(), $this->variantRules(false));
    }

    public function rulesForUpdate(): array
    {
        $productId = optional($this->route('product'))->id;
        return array_merge($this->baseProductRules($productId), $this->variantRules(true, $productId));
    }

    /** Rule chung cho products */
    protected function baseProductRules(?int $productId = null): array
    {
        $uniqueName = Rule::unique('products', 'name')->whereNull('deleted_at');
        $uniqueSlug = Rule::unique('products', 'slug')->whereNull('deleted_at');

        if ($productId) {
            $uniqueName = $uniqueName->ignore($productId);
            $uniqueSlug = $uniqueSlug->ignore($productId);
        }

        return [
            'name'        => ['required', 'string', 'max:255', $uniqueName],
            'slug'        => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $uniqueSlug],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'meta_title'  => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['required', 'in:0,1'],
            'is_featured' => ['required', 'in:0,1'],
            'price'       => ['required', 'integer', 'min:0'],
            'price_sale'  => ['nullable', 'integer', 'min:0', 'lte:price'],
        ];
    }

    /**
     * Rule cho biến thể
     * - Không bắt buộc SKU (vì tự sinh/đã có sẵn trong DB)
     */
    protected function variantRules(bool $isUpdate, ?int $productId = null): array
    {
        $rules = [
            'variants'                 => ['sometimes', 'array'],

            'variants.*.color_id'      => ['required_with:variants', 'nullable', 'integer', 'exists:colors,id'],
            'variants.*.size_id'       => ['required_with:variants', 'nullable', 'integer', 'exists:sizes,id'],
            'variants.*.texture_id'    => ['required_with:variants', 'nullable', 'integer', 'exists:textures,id'],

            // KHÔNG require sku ở form update/create (tự sinh ở server)
            'variants.*.sku'           => ['sometimes', 'string', 'max:255'], // nếu có gửi thì kiểm tra cơ bản

            'variants.*.price'         => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.quantity'      => ['nullable', 'integer', 'min:1'],
            'variants.*.image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.status'        => ['nullable', 'in:0,1'],
        ];

        if ($isUpdate) {
            $rules['variants.*.id'] = ['sometimes', 'integer', 'exists:product_variants,id'];
        }

        return $rules;
    }

    /**
     * Kiểm tra nâng cao:
     * - Nếu có biến thể => từng dòng phải đủ 3 thuộc tính
     * - Chặn trùng tổ hợp trong request
     * - Chặn trùng tổ hợp so với DB (cùng product), bỏ qua chính dòng đang update (nếu có id)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $variants = $this->input('variants', []);
            if (!is_array($variants) || empty($variants)) {
                return;
            }

            // 1) bắt buộc đủ 3 thuộc tính mỗi dòng nếu đã có biến thể
            foreach ($variants as $idx => $row) {
                $c = Arr::get($row, 'color_id');
                $s = Arr::get($row, 'size_id');
                $t = Arr::get($row, 'texture_id');

                if (!$c || !$s || !$t) {
                    $v->errors()->add("variants.$idx", 'Mỗi biến thể phải chọn đủ Màu sắc, Kích cỡ và Chất liệu.');
                }
            }

            // 2) chặn trùng tổ hợp trong cùng request
            $seen = [];
            foreach ($variants as $idx => $row) {
                $key = implode(':', [
                    Arr::get($row, 'color_id') ?: '0',
                    Arr::get($row, 'size_id') ?: '0',
                    Arr::get($row, 'texture_id') ?: '0',
                ]);
                if (isset($seen[$key])) {
                    $v->errors()->add("variants.$idx", 'Tổ hợp Màu/Size/Chất liệu bị trùng với dòng khác.');
                } else {
                    $seen[$key] = true;
                }
            }

            // 3) chặn trùng tổ hợp so với DB (cùng product)
            //    - Khi update: bỏ qua chính row nếu có id
            $productId = optional($this->route('product'))->id;
            if ($productId) {
                foreach ($variants as $idx => $row) {
                    $c = Arr::get($row, 'color_id');
                    $s = Arr::get($row, 'size_id');
                    $t = Arr::get($row, 'texture_id');
                    if (!$c || !$s || !$t) {
                        continue;
                    }

                    $query = DB::table('product_variants')
                        ->where('product_id', $productId)
                        ->where('color_id', $c)
                        ->where('size_id', $s)
                        ->where('texture_id', $t);

                    // bỏ qua chính mình nếu đang update dòng có id
                    $curId = Arr::get($row, 'id');
                    if ($curId) {
                        $query->where('id', '!=', (int)$curId);
                    }

                    if ($query->exists()) {
                        $v->errors()->add("variants.$idx", 'Tổ hợp Màu/Size/Chất liệu đã tồn tại trong sản phẩm.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Tên sản phẩm là bắt buộc.',
            'name.max'             => 'Tên sản phẩm tối đa :max ký tự.',
            'name.unique'          => 'Tên sản phẩm đã tồn tại.',
            'slug.required'        => 'Slug là bắt buộc.',
            'slug.regex'           => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
            'slug.unique'          => 'Slug đã tồn tại.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists'   => 'Danh mục không hợp lệ.',
            'thumbnail.image'      => 'Ảnh đại diện phải là định dạng ảnh.',
            'thumbnail.mimes'      => 'Ảnh đại diện chỉ chấp nhận jpg, jpeg, png, webp, gif.',
            'thumbnail.max'        => 'Ảnh đại diện tối đa 5MB.',
            'price.required'       => 'Giá gốc là bắt buộc.',
            'price.integer'        => 'Giá gốc phải là số.',
            'price.min'            => 'Giá gốc không được âm.',
            'price_sale.integer'   => 'Giá khuyến mãi phải là số.',
            'price_sale.lte'       => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
            'is_active.in'         => 'Trạng thái không hợp lệ.',
            'is_featured.in'       => 'Cờ nổi bật không hợp lệ.',

            'variants.array'                      => 'Dữ liệu biến thể không hợp lệ.',
            'variants.*.color_id.required_with'   => 'Thiếu màu sắc cho biến thể.',
            'variants.*.size_id.required_with'    => 'Thiếu kích cỡ cho biến thể.',
            'variants.*.texture_id.required_with' => 'Thiếu chất liệu cho biến thể.',

            // SKU không bắt buộc, nhưng nếu có gửi lên:
            'variants.*.sku.max'                  => 'SKU tối đa :max ký tự.',

            'variants.*.price.required_with'      => 'Giá của biến thể là bắt buộc.',
            'variants.*.price.integer'            => 'Giá của biến thể phải là số nguyên.',
            'variants.*.price.min'                => 'Giá của biến thể không được âm.',
            'variants.*.quantity.integer'         => 'Số lượng phải là số nguyên.',
            'variants.*.quantity.min'             => 'Số lượng tối thiểu là 1.',
            'variants.*.image.image'              => 'Ảnh biến thể phải là định dạng ảnh.',
            'variants.*.image.mimes'              => 'Ảnh biến thể chỉ chấp nhận jpg, jpeg, png, webp.',
            'variants.*.image.max'                => 'Ảnh biến thể tối đa 5MB.',
            'variants.*.status.in'                => 'Trạng thái biến thể không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'tên sản phẩm',
            'slug'        => 'slug',
            'category_id' => 'danh mục',
            'thumbnail'   => 'ảnh đại diện',
            'meta_title'  => 'tiêu đề SEO',
            'description' => 'mô tả',
            'price'       => 'giá gốc',
            'price_sale'  => 'giá khuyến mãi',

            'variants'                 => 'biến thể',
            'variants.*.color_id'      => 'màu sắc',
            'variants.*.size_id'       => 'kích cỡ',
            'variants.*.texture_id'    => 'chất liệu',
            'variants.*.sku'           => 'SKU',
            'variants.*.price'         => 'giá biến thể',
            'variants.*.quantity'      => 'số lượng',
            'variants.*.image'         => 'ảnh biến thể',
            'variants.*.status'        => 'trạng thái biến thể',
        ];
    }
}
