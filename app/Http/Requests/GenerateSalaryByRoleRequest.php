<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class GenerateSalaryByRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'role' => 'required|integer|in:' . \App\Models\User::ROLE_ADMIN . ',' . \App\Models\User::ROLE_STAFF . ',' . \App\Models\User::ROLE_WAREHOUSE_MANAGER,
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ];
    }
    


    public function messages()
    {
        return [
            'role.required' => 'Vui lòng chọn role',
            'role.in' => 'Role không hợp lệ',
            'month.required' => 'Vui lòng chọn tháng',
            'month.min' => 'Tháng phải từ 1 đến 12',
            'month.max' => 'Tháng phải từ 1 đến 12',
            'year.required' => 'Vui lòng nhập năm',
            'year.min' => 'Năm phải từ 2020 trở lên',
            'year.max' => 'Năm không được quá ' . (now()->year + 1),
        ];
    }
}