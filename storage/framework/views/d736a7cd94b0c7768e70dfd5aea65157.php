<?php $__env->startSection('title', 'Thông báo'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông báo</h1>
        <?php if($notifications->where('read_at', null)->count() > 0): ?>
            <form action="<?php echo e(route('admin.notifications.mark-all-read')); ?>" method="POST" style="display:inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
                <button type="submit" class="btn btn-sm btn-primary">Đánh dấu tất cả là đã đọc</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-sm <?php echo e(!request('type') ? 'btn-primary' : 'btn-outline-primary'); ?>">
            Tất cả (<?php echo e($typeCounts->sum()); ?>)
        </a>
        <a href="<?php echo e(route('admin.notifications.index', ['type' => 'low_stock'])); ?>" class="btn btn-sm <?php echo e(request('type') == 'low_stock' ? 'btn-danger' : 'btn-outline-danger'); ?>">
            Tồn kho thấp (<?php echo e($typeCounts['low_stock'] ?? 0); ?>)
        </a>
        <a href="<?php echo e(route('admin.notifications.index', ['type' => 'pending_approval'])); ?>" class="btn btn-sm <?php echo e(request('type') == 'pending_approval' ? 'btn-warning' : 'btn-outline-warning'); ?>">
            Chờ duyệt (<?php echo e($typeCounts['pending_approval'] ?? 0); ?>)
        </a>
        <a href="<?php echo e(route('admin.notifications.index', ['type' => 'qc_failed'])); ?>" class="btn btn-sm <?php echo e(request('type') == 'qc_failed' ? 'btn-danger' : 'btn-outline-danger'); ?>">
            QC Failed (<?php echo e($typeCounts['qc_failed'] ?? 0); ?>)
        </a>
        <a href="<?php echo e(route('admin.notifications.index', ['type' => 'count_discrepancy'])); ?>" class="btn btn-sm <?php echo e(request('type') == 'count_discrepancy' ? 'btn-warning' : 'btn-outline-warning'); ?>">
            Chênh lệch kiểm kê (<?php echo e($typeCounts['count_discrepancy'] ?? 0); ?>)
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if($notifications->isEmpty()): ?>
                <div class="alert alert-info">Không có thông báo nào.</div>
            <?php else: ?>
                <div class="list-group">
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item <?php echo e(is_null($notif->read_at) ? 'bg-light' : ''); ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <?php
                                            $badgeColor = match($notif->type) {
                                                'low_stock', 'insufficient_stock', 'qc_failed' => 'danger',
                                                'pending_approval', 'count_discrepancy' => 'warning',
                                                'new_order', 'defect_found' => 'info',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($badgeColor); ?>"><?php echo e($notif->type); ?></span>
                                        <?php echo e($notif->title); ?>

                                    </h6>
                                    <p class="mb-1"><?php echo e($notif->message); ?></p>
                                    <small class="text-muted"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                                </div>
                                <?php if(is_null($notif->read_at)): ?>
                                    <form action="<?php echo e(route('admin.notifications.mark-read', $notif->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Đánh dấu đã đọc</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-4">
                    <?php echo e($notifications->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX-main\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>