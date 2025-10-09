<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateLoyaltyTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kiểm tra xem người dùng có quyền Admin để cập nhật không
        return true;
    }

    public function rules(): array
    {
        // Lấy ID của đối tượng LoyaltyTier đang được chỉnh sửa từ route
        $loyaltyTierId = $this->route('loyaltyTier')->id;

        return [
            // Kiểm tra UNIQUE, nhưng bỏ qua chính ID của đối tượng này
            'name' => ['required', 'string', 'max:50', Rule::unique('loyalty_tiers', 'name')->ignore($loyaltyTierId)],
            'min_spend_required' => 'required|numeric|min:0',
            'discount_rate' => 'required|numeric|between:0,100',
        ];
    }
}
