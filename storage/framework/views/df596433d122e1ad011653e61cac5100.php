<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <h3>Thêm phần thưởng vòng quay</h3>

        <form action="<?php echo e(route('admin.spin.store')); ?>" method="POST" class="mt-3">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label">Tên phần thưởng</label>
                <input type="text" name="name" class="form-control" required value="<?php echo e(old('name')); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Loại phần thưởng</label>
                <select name="type" class="form-select" required id="typeSelect">
                    <option value="">-- Chọn loại --</option>
                    <option value="VOUCHER">Voucher</option>
                    <option value="LOYALTY_POINTS">Điểm thưởng</option>
                    <option value="NONE">Khác</option>
                </select>
            </div>

            <div class="mb-3" id="voucherSelect" style="display:none;">
                <label class="form-label">Voucher</label>
                <select name="value_reference" class="form-select">
                    <option value="">-- Chọn voucher --</option>
                    <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3" id="pointInput" style="display:none;">
                <label class="form-label">Giá trị điểm</label>
                <input type="number" name="value_reference" class="form-control" step="1" placeholder="Nhập điểm thưởng">
            </div>

            <div class="mb-3">
                <label class="form-label">Xác suất (0.0000 - 1.0000)</label>
                <input type="number" name="probability" class="form-control" step="0.0001" required>
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="<?php echo e(route('admin.spin.index')); ?>" class="btn btn-secondary">Hủy</a>
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

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/spin/create.blade.php ENDPATH**/ ?>