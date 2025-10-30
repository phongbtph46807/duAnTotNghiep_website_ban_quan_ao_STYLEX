<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <h3>Chỉnh sửa phần thưởng</h3>

        <form action="<?php echo e(route('admin.spin.update', $spin->id)); ?>" method="POST" class="mt-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-3">
                <label class="form-label">Tên phần thưởng</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $spin->name)); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại phần thưởng</label>
                <select name="type" class="form-select" id="typeSelect">
                    <option value="VOUCHER" <?php echo e($spin->type == 'VOUCHER' ? 'selected' : ''); ?>>Voucher</option>
                    <option value="LOYALTY_POINTS" <?php echo e($spin->type == 'LOYALTY_POINTS' ? 'selected' : ''); ?>>Điểm thưởng</option>
                    <option value="NONE" <?php echo e($spin->type == 'NONE' ? 'selected' : ''); ?>>Khác</option>
                </select>
            </div>

            <div class="mb-3" id="voucherSelect" style="display: <?php echo e($spin->type == 'VOUCHER' ? 'block' : 'none'); ?>">
                <label class="form-label">Voucher</label>
                <select name="value_reference" class="form-select">
                    <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" <?php echo e($spin->value_reference == $v->id ? 'selected' : ''); ?>>
                            <?php echo e($v->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3" id="pointInput" style="display: <?php echo e($spin->type == 'LOYALTY_POINTS' ? 'block' : 'none'); ?>">
                <label class="form-label">Giá trị điểm</label>
                <input type="number" name="value_reference" class="form-control"
                       value="<?php echo e(old('value_reference', $spin->value_reference)); ?>" step="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Xác suất</label>
                <input type="number" name="probability" class="form-control" step="0.0001"
                       value="<?php echo e(old('probability', $spin->probability)); ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="<?php echo e(route('admin.spin.index')); ?>" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script>
        document.getElementById('typeSelect').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('voucherSelect').style.display = (type === 'VOUCHER') ? 'block' : 'none';
            document.getElementById('pointInput').style.display = (type === 'LOYALTY_POINTS') ? 'block' : 'none';
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/spin/edit.blade.php ENDPATH**/ ?>