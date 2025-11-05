<?php echo csrf_field(); ?>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Mã voucher</label>
        <input type="text" name="code" value="<?php echo e(old('code', $voucher->code ?? '')); ?>" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Loại</label>
        <select name="type" class="form-select" required>
            <option value="percent" <?php echo e(old('type', $voucher->type ?? 'percent') === 'percent' ? 'selected' : ''); ?>>Phần trăm</option>
            <option value="fixed" <?php echo e(old('type', $voucher->type ?? '') === 'fixed' ? 'selected' : ''); ?>>Cố định</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Giá trị</label>
        <input type="number" step="0.01" min="0" name="value" value="<?php echo e(old('value', $voucher->value ?? 0)); ?>" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Giảm tối đa (áp dụng với %)</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" value="<?php echo e(old('max_discount_amount', $voucher->max_discount_amount ?? '')); ?>" class="form-control" placeholder="Không giới hạn nếu bỏ trống">
    </div>

    <div class="col-md-6">
        <label class="form-label">Mô tả</label>
        <input type="text" name="description" value="<?php echo e(old('description', $voucher->description ?? '')); ?>" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Đơn tối thiểu</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="<?php echo e(old('min_order_amount', $voucher->min_order_amount ?? '')); ?>" class="form-control" placeholder="Không bắt buộc">
    </div>

    <div class="col-md-6">
        <label class="form-label">Giới hạn lượt dùng</label>
        <input type="number" min="1" name="usage_limit" value="<?php echo e(old('usage_limit', $voucher->usage_limit ?? '')); ?>" class="form-control" placeholder="Không giới hạn nếu bỏ trống">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?php echo e(old('is_active', ($voucher->is_active ?? true)) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="is_active">Kích hoạt</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Bắt đầu</label>
        <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', isset($voucher->starts_at) ? $voucher->starts_at->format('Y-m-d\TH:i') : '')); ?>" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Kết thúc</label>
        <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', isset($voucher->ends_at) ? $voucher->ends_at->format('Y-m-d\TH:i') : '')); ?>" class="form-control">
    </div>
    <?php if($errors->any()): ?>
        <div class="col-12">
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <button class="btn btn-primary">Lưu</button>
        <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="btn btn-light">Hủy</a>
    </div>
</div>


<?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/vouchers/_form.blade.php ENDPATH**/ ?>