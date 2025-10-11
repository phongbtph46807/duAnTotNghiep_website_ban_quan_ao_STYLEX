<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|max:255',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured' =>'required|in:1,0',
            'status' => 'required|in:active,inactive',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.max'      => 'Tên sản phẩm không được quá 255 kí tự',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 255 kí tự',
            'category_id.required'  => 'Danh mục không được để trống',
            'category_id.exists'    => 'Danh mục không tồn tại',
            'thumbnail.image'       =>'Không đúng định dạng ảnh',
            'thumbnail.mimes'       =>'Không đúng loại ảnh được upload',
            'thumbnail.max'         =>'Ảnh không được quá 2048MB',
            'is_featured'            =>'Vui lòng thêm trạng thái sản phẩm nổi bật',
            'is_featured.in'         =>'Trạng thái không hợp lệ',
            'status.required'       =>'Trạng thái là bắt buộc'
        ];
    }
}
