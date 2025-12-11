<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0|lte:price',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.texture_id' => 'nullable|exists:textures,id',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.quantity' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.status' => 'boolean',
        ];
    }
    public function messages()
    {
        return [
            // Product
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'slug.string' => 'Slug phải là chuỗi ký tự.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug đã tồn tại, vui lòng chọn slug khác.',

            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',

            'thumbnail.image' => 'Ảnh đại diện phải là định dạng hình ảnh.',
            'thumbnail.mimes' => 'Ảnh đại diện chỉ chấp nhận các định dạng jpeg, png, jpg, gif.',
            'thumbnail.max' => 'Ảnh đại diện không được vượt quá 2MB.',

            'product_images.*.image' => 'Mỗi ảnh sản phẩm phải là định dạng hình ảnh.',
            'product_images.*.mimes' => 'Ảnh sản phẩm chỉ chấp nhận các định dạng jpeg, png, jpg, gif.',
            'product_images.*.max' => 'Ảnh sản phẩm không được vượt quá 2MB.',

            'alt_texts.*.string' => 'Alt text phải là chuỗi ký tự.',
            'alt_texts.*.max' => 'Alt text không được vượt quá 255 ký tự.',

            'description.string' => 'Mô tả phải là chuỗi ký tự.',

            'price.required' => 'Giá sản phẩm không được để trống.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',

            'price_sale.numeric' => 'Giá khuyến mãi phải là số.',
            'price_sale.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'price_sale.lte' => 'Giá khuyến mãi không được lớn hơn giá gốc.',

            'is_active.boolean' => 'Trạng thái hoạt động không hợp lệ.',
            'is_featured.boolean' => 'Trạng thái nổi bật không hợp lệ.',

            // Variants
            'variants.*.color_id.exists' => 'Màu sắc của biến thể không hợp lệ.',
            'variants.*.size_id.exists' => 'Kích thước của biến thể không hợp lệ.',
            'variants.*.texture_id.exists' => 'Chất liệu của biến thể không hợp lệ.',

            'variants.*.price.numeric' => 'Giá của biến thể phải là số.',
            'variants.*.price.min' => 'Giá của biến thể không được nhỏ hơn 0.',

            'variants.*.quantity.integer' => 'Số lượng biến thể phải là số nguyên.',
            'variants.*.quantity.min' => 'Số lượng biến thể không được nhỏ hơn 0.',

            'variants.*.image.image' => 'Ảnh biến thể phải là định dạng hình ảnh.',
            'variants.*.image.mimes' => 'Ảnh biến thể chỉ chấp nhận các định dạng jpeg, png, jpg, gif.',
            'variants.*.image.max' => 'Ảnh biến thể không được vượt quá 2MB.',

            'variants.*.status.boolean' => 'Trạng thái biến thể không hợp lệ.',
        ];
    }
}
