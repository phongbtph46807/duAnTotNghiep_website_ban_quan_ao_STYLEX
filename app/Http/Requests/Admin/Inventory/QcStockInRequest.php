<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;


class QcStockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('stock_in.qc');
    }

    public function rules(): array
    {
        // Lấy thông tin yêu cầu nhập kho từ route parameter
        $stockInRequest = $this->route('stockInRequest');

        // Đảm bảo rằng $stockInRequest tồn tại và có thuộc tính quantity
        return [
            'qc_passed_qty' => 'required|integer|min:0|max:' . $stockInRequest->quantity,
            'qc_failed_qty' => 'required|integer|min:0|max:' . $stockInRequest->quantity,
            'qc_notes' => 'nullable|string|max:1000',
            'defect_reasons' => 'nullable|array',
            'defect_reasons.*' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'qc_passed_qty.required' => 'Số lượng đạt QC là bắt buộc',
            'qc_passed_qty.integer' => 'Số lượng đạt QC phải là số nguyên',
            'qc_passed_qty.min' => 'Số lượng đạt QC không được âm',
            'qc_passed_qty.max' => 'Số lượng đạt QC không được vượt quá :max',
            'qc_failed_qty.required' => 'Số lượng không đạt QC là bắt buộc',
            'qc_failed_qty.integer' => 'Số lượng không đạt QC phải là số nguyên',
            'qc_failed_qty.min' => 'Số lượng không đạt QC không được âm',
            'qc_failed_qty.max' => 'Số lượng không đạt QC không được vượt quá :max',
            'qc_notes.max' => 'Ghi chú QC không được vượt quá 1000 ký tự',
            'defect_reasons.*.max' => 'Lý do lỗi không được vượt quá 255 ký tự',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $stockInRequest = $this->route('stockInRequest');
            $qcPassed = $this->input('qc_passed_qty', 0);
            $qcFailed = $this->input('qc_failed_qty', 0);

            if ($qcPassed + $qcFailed !== $stockInRequest->quantity) {
                $validator->errors()->add('qc_passed_qty',
                    'Tổng số lượng QC phải bằng số lượng nhập kho (' . $stockInRequest->quantity . ')');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'qc_passed_qty' => 'Số lượng đạt QC',
            'qc_failed_qty' => 'Số lượng không đạt QC',
            'qc_notes' => 'Ghi chú QC',
            'defect_reasons' => 'Lý do lỗi',
        ];
    }
}
