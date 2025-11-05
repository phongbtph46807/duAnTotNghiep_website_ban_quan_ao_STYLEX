

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h3 class="mb-3">Tạo voucher</h3>
    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('admin.vouchers.store')); ?>" method="POST">
                <?php echo $__env->make('admin.vouchers._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/vouchers/create.blade.php ENDPATH**/ ?>