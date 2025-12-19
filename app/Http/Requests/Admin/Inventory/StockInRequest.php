<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'batch_number' => ['required', 'string', 'unique:stock_in_requests,batch_number'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'received_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'warehouse_id' => 'Kho nhập',
            'variant_id' => 'Sản phẩm',
            'batch_number' => 'Mã lô',
            'quantity' => 'Số lượng',
            'cost_price' => 'Giá nhập',
            'received_date' => 'Ngày nhận hàng',
            'notes' => 'Ghi chú',
        ];
    }
}
