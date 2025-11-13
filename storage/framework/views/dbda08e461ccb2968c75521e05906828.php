<?php $__env->startSection('title', 'Lịch sử quay thưởng'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container my-5">
        <h2 class="mb-4">📜 Lịch sử quay thưởng của bạn</h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Phần thưởng</th>
                            <th>Voucher</th>
                            <th>Trạng thái</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $userSpins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item->created_at->format('d/m/Y H:i')); ?></td>
                                <td><strong><?php echo e($item->spin->name); ?></strong></td>
                                <td>
                                    <?php if($item->spin->voucher): ?>
                                        <span class="badge bg-primary"><?php echo e($item->spin->voucher->code); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">---</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($item->is_claimed): ?>
                                        <span class="badge bg-success">Đã nhận</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chưa nhận</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có lịch sử quay</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <?php echo e($userSpins->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/spins/history.blade.php ENDPATH**/ ?>