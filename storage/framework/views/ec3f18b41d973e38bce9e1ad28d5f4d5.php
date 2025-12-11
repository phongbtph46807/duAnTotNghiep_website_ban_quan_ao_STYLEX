<?php $__env->startSection('title', 'Cài đặt Kho hàng'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h2 class="h3 mb-3">Cài đặt Kho hàng</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.inventory.settings.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-warehouse"></i> Cài đặt Kho hàng</h6>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-bell"></i> Cảnh báo Tồn kho</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ngưỡng tồn kho thấp</label>
                                <input type="number" class="form-control form-control-sm" name="low_stock_threshold"
                                       value="<?php echo e(old('low_stock_threshold', $settings['low_stock_threshold'] ?? 10)); ?>" min="0">
                                <small class="text-muted">Cảnh báo khi tồn kho ≤ giá trị này</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ngưỡng QC Failed (%)</label>
                                <input type="number" class="form-control form-control-sm" name="qc_failed_threshold"
                                       value="<?php echo e(old('qc_failed_threshold', $settings['qc_failed_threshold'] ?? 10)); ?>" min="0" max="100">
                                <small class="text-muted">Cảnh báo khi tỷ lệ hỏng ≥ giá trị này</small>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ngưỡng chênh lệch kiểm kê</label>
                                <input type="number" class="form-control form-control-sm" name="count_discrepancy_threshold"
                                       value="<?php echo e(old('count_discrepancy_threshold', $settings['count_discrepancy_threshold'] ?? 5)); ?>" min="0">
                                <small class="text-muted">Cảnh báo khi chênh lệch ≥ giá trị này</small>
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-toggle-on"></i> Bật/Tắt Thông báo</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_new_order" name="notify_new_order" value="1"
                                           <?php echo e(($settings['notify_new_order'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_new_order">Đơn hàng mới</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_low_stock" name="notify_low_stock" value="1"
                                           <?php echo e(($settings['notify_low_stock'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_low_stock">Tồn kho thấp</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_pending_approval" name="notify_pending_approval" value="1"
                                           <?php echo e(($settings['notify_pending_approval'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_pending_approval">Phiếu chờ duyệt</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_qc_failed" name="notify_qc_failed" value="1"
                                           <?php echo e(($settings['notify_qc_failed'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_qc_failed">QC Failed cao</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_count_discrepancy" name="notify_count_discrepancy" value="1"
                                           <?php echo e(($settings['notify_count_discrepancy'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_count_discrepancy">Chênh lệch kiểm kê</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify_defect_found" name="notify_defect_found" value="1"
                                           <?php echo e(($settings['notify_defect_found'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label small" for="notify_defect_found">Hàng hỏng</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-broom"></i> Tự động Dọn dẹp</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Xóa thông báo đã đọc sau (ngày)</label>
                                <input type="number" class="form-control form-control-sm" name="notification_cleanup_read_days"
                                       value="<?php echo e(old('notification_cleanup_read_days', $settings['notification_cleanup_read_days'] ?? 30)); ?>" min="1">
                                <small class="text-muted">Mặc định: 30 ngày</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Xóa thông báo chưa đọc sau (ngày)</label>
                                <input type="number" class="form-control form-control-sm" name="notification_cleanup_unread_days"
                                       value="<?php echo e(old('notification_cleanup_unread_days', $settings['notification_cleanup_unread_days'] ?? 90)); ?>" min="1">
                                <small class="text-muted">Mặc định: 90 ngày</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-box"></i> Tồn kho</h6>
                    </div>
                    <div class="card-body p-3">
                        <?php
                            $totalStock = \Illuminate\Support\Facades\DB::table('warehouse_stocks')->sum('on_hand') ?? 0;
                        ?>
                        <div class="text-center">
                            <h2 class="text-primary mb-2"><?php echo e(number_format($totalStock)); ?></h2>
                            <p class="text-muted mb-0">Tổng số sản phẩm trong kho</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">
                    <i class="fas fa-save"></i> Lưu cài đặt
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX-main\resources\views/admin/inventory/settings.blade.php ENDPATH**/ ?>