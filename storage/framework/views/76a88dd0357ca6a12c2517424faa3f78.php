<?php $__env->startSection('content'); ?>
    <div class="row gx-4">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="row g-4 mb-4">
                            <div class="col-xxl-3 col-lg-5">
                                <img src="assets/images/blog/img-1.jpg" alt=""
                                     class="img-fluid rounded w-100 object-fit-cover">
                            </div>
                            <div class="col-xxl-9 col-lg-7">
                                <a href="pages-blog-overview.html">
                                    <h5 class="fs-15 fw-semibold"><?php echo e($post->title); ?></h5>
                                </a>
                                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                <span class="text-muted">
                                    <i class="ri-calendar-event-line me-1"></i> 05 Apr, 2024
                                </span>
                                    |
                                    <a href="pages-profile.html">
                                        <i class="ri-user-3-line me-1"></i> <?php echo e($post->author->name); ?>

                                    </a>
                                </div>
                                <p class="text-muted mb-2"><?php echo $post->content; ?></p>
                                <a href="pages-blog-overview.html" class="text-decoration-underline">Read more <i
                                        class="ri-arrow-right-line"></i></a>
                                <div class="mt-3">
                                    <a href="<?php echo e(route('admin.post.edit', $post->id)); ?>" class="btn btn-warning btn-sm me-2">
                                        Edit
                                    </a>
                                    <form action="<?php echo e(route('admin.post.destroy', $post->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\admin\post\index.blade.php ENDPATH**/ ?>