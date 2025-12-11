<?php

namespace App\Http\Requests\Admin\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('warehouses', 'name')->ignore($warehouseId)
            ],
            'type' => [
                'required',
                Rule::in(['PHYSICAL', 'VIRTUAL', 'CONSIGNMENT', 'SCRAP'])
            ],
            'operational_status' => [
                'required',
                Rule::in(['ACTIVE', 'INACTIVE', 'MAINTENANCE'])
            ],
            'address' => [
                'nullable',
                'string',
                'max:500'
            ],
        ];

        // Chỉ validate code khi tạo mới, không validate khi update
        if (!$isUpdate) {
            $rules['code'] = [
                'required',
                'string',
                'min:2',
                'max:50',
                'uppercase',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('warehouses', 'code')
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên kho hàng',
            'code' => 'mã kho hàng',
            'type' => 'loại kho',
            'operational_status' => 'trạng thái hoạt động',
            'address' => 'địa chỉ',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập :attribute.',
            'name.min' => ':attribute phải có ít nhất :min ký tự.',
            'name.max' => ':attribute không được vượt quá :max ký tự.',
            'name.unique' => ':attribute đã tồn tại trong hệ thống.',
            
            'code.required' => 'Vui lòng nhập :attribute.',
            'code.min' => ':attribute phải có ít nhất :min ký tự.',
            'code.max' => ':attribute không được vượt quá :max ký tự.',
            'code.uppercase' => ':attribute phải viết hoa.',
            'code.regex' => ':attribute chỉ được chứa chữ in hoa, số, gạch ngang và gạch dưới.',
            'code.unique' => ':attribute đã tồn tại trong hệ thống.',
            
            'type.required' => 'Vui lòng chọn :attribute.',
            'type.in' => ':attribute không hợp lệ.',
            
            'operational_status.required' => 'Vui lòng chọn :attribute.',
            'operational_status.in' => ':attribute không hợp lệ.',
            
            'address.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }
}
