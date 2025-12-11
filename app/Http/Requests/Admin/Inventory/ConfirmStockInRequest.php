<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmStockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('stock_in.confirm');
    }

    public function rules(): array
    {
        return [
            'confirm_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'confirm_notes' => 'Ghi chú xác nhận',
        ];
    }
}