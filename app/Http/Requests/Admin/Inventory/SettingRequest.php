<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Giả định admin đã đăng nhập
    }

    public function rules(): array
    {
        return [
            // Ngưỡng cảnh báo
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
            'qc_failed_threshold' => ['required', 'integer', 'min:0', 'max:100'],
            'count_discrepancy_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
            
            // Bật/Tắt thông báo
            'notify_new_order' => ['nullable', 'boolean'],
            'notify_low_stock' => ['nullable', 'boolean'],
            'notify_pending_approval' => ['nullable', 'boolean'],
            'notify_qc_failed' => ['nullable', 'boolean'],
            'notify_count_discrepancy' => ['nullable', 'boolean'],
            'notify_defect_found' => ['nullable', 'boolean'],
            
            // Cleanup
            'notification_cleanup_read_days' => ['required', 'integer', 'min:1', 'max:365'],
            'notification_cleanup_unread_days' => ['required', 'integer', 'min:1', 'max:365'],
        ];
    }

    public function attributes(): array
    {
        return [
            'low_stock_threshold' => 'Ngưỡng tồn kho thấp',
            'qc_failed_threshold' => 'Ngưỡng QC Failed',
            'count_discrepancy_threshold' => 'Ngưỡng chênh lệch kiểm kê',
            'notify_new_order' => 'Đơn hàng mới',
            'notify_low_stock' => 'Tồn kho thấp',
            'notify_pending_approval' => 'Phiếu chờ duyệt',
            'notify_qc_failed' => 'QC Failed',
            'notify_count_discrepancy' => 'Chênh lệch kiểm kê',
            'notify_defect_found' => 'Hàng hỏng',
            'notification_cleanup_read_days' => 'Số ngày xóa thông báo đã đọc',
            'notification_cleanup_unread_days' => 'Số ngày xóa thông báo chưa đọc',
        ];
    }
}
