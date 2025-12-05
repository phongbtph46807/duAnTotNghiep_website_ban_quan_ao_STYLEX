<?php $__env->startSection('title', 'Chi tiết đánh giá'); ?>

<?php $__env->startPush('page-css'); ?>
    <link href="<?php echo e(asset('assets/css/custom.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        .review-detail-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            background: #fff;
        }
        .review-rating {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .star {
            color: #ffc107;
            font-size: 24px;
        }
        .star.empty {
            color: #e2e8f0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chi tiết đánh giá</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.reviews.index')); ?>">Quản lý đánh giá</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="review-detail-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="mb-2">Thông tin người đánh giá</h5>
                        <p class="mb-1"><strong>Tên:</strong> <?php echo e($review->user->name ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo e($review->user->email ?? 'N/A'); ?></p>
                        <p class="mb-0"><strong>Ngày đánh giá:</strong> <?php echo e($review->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div>
                        <span class="badge bg-<?php echo e($review->status === 'approved' ? 'success' : ($review->status === 'pending' ? 'warning' : 'danger')); ?>">
                            <?php if($review->status === 'pending'): ?>
                                Chờ duyệt
                            <?php elseif($review->status === 'approved'): ?>
                                Đã duyệt
                            <?php else: ?>
                                Đã từ chối
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <h5 class="mb-3">Đánh giá</h5>
                    <div class="review-rating mb-3">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo e($i <= $review->rating ? '' : 'empty'); ?>">★</span>
                        <?php endfor; ?>
                        <span style="margin-left:12px;font-weight:600;font-size:18px;"><?php echo e($review->rating); ?>/5</span>
                    </div>
                    <?php if($review->content): ?>
                        <div class="p-3 bg-light rounded">
                            <?php echo e($review->content); ?>

                        </div>
                    <?php else: ?>
                        <p class="text-muted">Không có nội dung đánh giá</p>
                    <?php endif; ?>
                </div>

                <hr>

                <div class="mb-4">
                    <h5 class="mb-3">Thông tin sản phẩm</h5>
                    <p class="mb-1"><strong>Tên sản phẩm:</strong> <?php echo e($review->product->name ?? 'N/A'); ?></p>
                    <?php if($review->productVariant): ?>
                        <p class="mb-1"><strong>Biến thể:</strong> <?php echo e($review->productVariant->attribute_summary ?? 'N/A'); ?></p>
                    <?php endif; ?>
                    <?php if($review->order_id): ?>
                        <p class="mb-0"><strong>Mã đơn hàng:</strong> <?php echo e($review->order->code ?? 'N/A'); ?></p>
                    <?php endif; ?>
                </div>

                <?php if($review->media && $review->media->count() > 0): ?>
                    <hr>
                    <div class="mb-4">
                        <h5 class="mb-3">Hình ảnh đánh giá</h5>
                        <div class="row g-3">
                            <?php $__currentLoopData = $review->media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3">
                                    <img src="<?php echo e(asset('storage/' . $media->path)); ?>" 
                                         alt="Review image" 
                                         class="img-fluid rounded"
                                         style="max-height:200px;object-fit:cover;">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($review->experiences && $review->experiences->count() > 0): ?>
                    <hr>
                    <div class="mb-4">
                        <h5 class="mb-3">Trải nghiệm</h5>
                        <ul class="list-unstyled">
                            <?php $__currentLoopData = $review->experiences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $experience): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="mb-2">
                                    <i class="ri-check-line text-success"></i> <?php echo e($experience->description); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?php echo e(route('admin.reviews.index')); ?>" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Quay lại
                    </a>
                    <form action="<?php echo e(route('admin.reviews.destroy', $review->id)); ?>" 
                          method="POST" 
                          style="display:inline;"
                          onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line"></i> Xóa đánh giá
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\admin\reviews\show.blade.php ENDPATH**/ ?>