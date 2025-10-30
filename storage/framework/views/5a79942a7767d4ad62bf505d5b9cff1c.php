<?php $__env->startSection('title', $post->title . ' - ' . env('APP_NAME')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="display-5 mb-3"><?php echo e($post->title); ?></h1>
                <div class="d-flex justify-content-between align-items-center mb-3 text-muted border-bottom pb-3">
                    <small>
                        <?php if($post->author): ?>
                            Đăng bởi: <strong><?php echo e($post->author->name); ?></strong>
                        <?php else: ?>
                            Đăng bởi: <strong>Vô danh</strong>
                        <?php endif; ?>
                    </small>
                    <small>
                        Ngày đăng: <?php echo e($post->created_at->format('d/m/Y H:i')); ?>

                    </small>
                </div>

                <div class="article-content">
                    <?php echo $post->content; ?>

                </div>

                <hr class="my-4">

                <a href="<?php echo e(route('client.posts.index')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>

            </div>
        </div>
    </div>
    <?php if($recentPosts->isNotEmpty()): ?>
        <div class="row mt-5 pt-4 border-top">
            <div class="col-12">
                <h2 class="text-center mb-4">Bài viết khác</h2>
            </div>

            <?php $__currentLoopData = $recentPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4">
                    <div class="card shadow-sm border-0 rounded-3 w-100 position-relative">

                        <?php if($recentPost->image): ?>
                            <img src="<?php echo e($recentPost->image); ?>"
                                 class="card-img-top"
                                 alt="<?php echo e($recentPost->title); ?>"
                                 style="object-fit: cover; height: 200px;"
                                 onerror="this.src='https://placehold.co/600x400/EBF2FA/7F8A9A?text=H%C3%ACnh+%E1%BA%A3nh+l%E1%BB%97i'; this.onerror=null;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center"
                                 style="height: 200px; background-color: #f8f9fa;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#adb5bd" class="bi bi-newspaper" viewBox="0 0 16 16">
                                    <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.11.995l-.35.753-2.336-4.31A1.5 1.5 0 0 0 9.15 8.98l-1.622 1.956-1.39-2.78a1.5 1.5 0 0 0-1.387-.995L.11 14.249a1.5 1.5 0 0 1-.11-.995V2.5zM1.5 2a.5.5 0 0 0-.5.5v11.5a.5.5 0 0 0 .017.07l.006.014 3.68-1.55L7.29 9.82a.5.5 0 0 1 .494.007l1.35 2.7 1.12-1.31a.5.5 0 0 1 .494-.007l2.35 4.227.005.008.007.004a.5.5 0 0 0 .03.001.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-.5-.5h-11z"/>
                                    <path d="M3 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zM3 5.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zM3 7.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="<?php echo e(route('client.posts.show', $recentPost->slug)); ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo e($recentPost->title); ?>

                                </a>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo e(Str::limit(strip_tags($recentPost->content), 80, '...')); ?>

                            </p>
                            <hr>
                            <small class="text-muted">
                                <?php echo e($recentPost->created_at->format('d/m/Y')); ?>

                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/posts/detail.blade.php ENDPATH**/ ?>