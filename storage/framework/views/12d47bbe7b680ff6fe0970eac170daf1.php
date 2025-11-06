<?php $__env->startSection('title', 'Quản lí liên hệ'); ?>
<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Danh sách liên hệ</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="listjs-table" id="customerList">
                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle text-center table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th data-sort="customer_id">ID</th>
                                <th data-sort="customer_name">Email</th>
                                <th data-sort="email">Tin nhắn</th>
                            </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="id">
                                        #<?php echo e($contact->id); ?>

                                    </td>
                                    <td class="customer_name"><?php echo e($contact->email); ?></td>
                                    <td class="email"><?php echo e($contact->content); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/contact/index.blade.php ENDPATH**/ ?>