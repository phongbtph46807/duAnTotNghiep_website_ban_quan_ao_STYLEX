
<?php $__env->startSection('title', 'Danh sách đánh giá'); ?>
<?php $__env->startPush('page-css'); ?>
    <link href="<?php echo e(asset('assets/css/custom.css')); ?>" rel="stylesheet" type="text/css" />

    <style>
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 150px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .user-table th,
        .user-table td {
            vertical-align: middle;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lí đánh giá</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Quản lí đánh giá</a></li>
                        <li class="breadcrumb-item">Danh sách đánh giá</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row cursor-pointer">
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card total-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-primary">
                        <i class="la la-images"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng số đánh giá</h5>
                    <h3 class="card-text fw-bold"><?php echo e($summary['total'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card approved-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-success">
                        <i class="la la-check-circle"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số đánh giá được hiển thị</h5>
                    <h3 class="card-text fw-bold text-success"><?php echo e($summary['public'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card pending-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-warning">
                        <i class="la la-ban"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số đánh giá bị ẩn</h5>
                    <h3 class="card-text fw-bold text-warning"><?php echo e($summary['hidden'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card rejected-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-danger">
                        <i class="la la-trash"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số đánh giá xấu (< 3 sao)</h5>
                            <h3 class="card-text fw-bold text-danger">
                                <?php echo e($summary['bad_reviews'] ?? 0); ?>

                            </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách đánh giá</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div><!-- end card header -->

                
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="<?php echo e(route('admin.reviews.index')); ?>" method="GET">
                        <div class="row g-3">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Người đánh giá</label>
                                <input type="text" name="us" value="<?php echo e(request('us')); ?>" class="form-control"
                                    placeholder="Nhập tên người dùng">
                            </div>

                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Sản phẩm</label>
                                <select name="product" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id); ?>"
                                            <?php echo e(request('product') == $p->id ? 'selected' : ''); ?>>
                                            <?php echo e($p->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="public" <?php echo e(request('status') == 'public' ? 'selected' : ''); ?>>Public
                                    </option>
                                    <option value="hidden" <?php echo e(request('status') == 'hidden' ? 'selected' : ''); ?>>Hidden
                                    </option>
                                </select>
                            </div>

                            
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Số sao</label>
                                <select name="rating" class="form-select">
                                    <option value="">-- Số sao --</option>
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo e($i); ?>"
                                            <?php echo e(request('rating') == $i ? 'selected' : ''); ?>>
                                            <?php echo e($i); ?> sao
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('admin.reviews.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="listjs-table" id="customerList">

                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle table-nowrap" id="customerTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>STT</th>
                                        <th data-sort="customer_name">Người đánh giá</th>
                                        <th data-sort="cate">Sản phẩm</th>
                                        <th data-sort="phone">Số sao</th>
                                        <th data-sort="date">Trải nghiệm</th>
                                        <th data-sort="phone">Trạng thái</th>
                                        <th data-sort="phone">Ngày gửi</th>
                                        <th data-sort="action">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b">

                                            
                                            <td class="text-center">
                                                <?php echo e($reviews->firstItem() + $index); ?>

                                            </td>

                                            
                                            <td>
                                                <?php if($review->user): ?>
                                                    <img src="<?php echo e($review->user->avatar ?? 'https://res.cloudinary.com/dvrexlsgx/image/upload/v1732148083/Avatar-trang-den_apceuv_pgbce6.png'); ?>"
                                                        width="50px">
                                                <?php endif; ?>
                                                <?php echo e($review->user->name ?? 'Ẩn danh'); ?>


                                            </td>

                                            
                                            <td>
                                                <?php echo e($review->product->name ?? '---'); ?> <br>
                                                <?php echo e($review->productVariant->attribute_summary ?? '---'); ?>

                                            </td>

                                            
                                            <td>
                                                <div class="flex items-center">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <svg class="<?php echo e($i <= $review->rating ? 'text-warning' : 'text-light'); ?>"
                                                            viewBox="0 0 24 24"
                                                            style="width:20px;height:20px;fill:currentColor;">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                        9.24l-7.19-.61L12 2 9.19 8.63
                                                        2 9.24l5.46 4.73L5.82 21z" />
                                                        </svg>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>

                                            
                                            <?php
                                                // lấy bản ghi tương ứng cho từng tiêu chí (nếu có)
                                                $expFabric = $review->experiences->firstWhere(
                                                    'criterion',
                                                    'Chất liệu vải',
                                                );
                                                $expFit = $review->experiences->firstWhere('criterion', 'Độ vừa vặn');
                                                $expColor = $review->experiences->firstWhere('criterion', 'Màu sắc');

                                                $fabricRating = $expFabric->rating ?? 0;
                                                $fitRating = $expFit->rating ?? 0;
                                                $colorRating = $expColor->rating ?? 0;
                                            ?>
                                            <td>
                                                Chất liệu vải :
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="<?php echo e($i <= $fabricRating ? 'text-warning' : 'text-light'); ?>"
                                                        viewBox="0 0 24 24"
                                                        style="width:20px;height:20px;fill:currentColor;">
                                                        <path
                                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                    </svg>
                                                <?php endfor; ?>
                                                <br>
                                                Độ vừa vặn : <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="<?php echo e($i <= $fitRating ? 'text-warning' : 'text-light'); ?>"
                                                        viewBox="0 0 24 24"
                                                        style="width:20px;height:20px;fill:currentColor;">
                                                        <path
                                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                    </svg>
                                                <?php endfor; ?> <br>
                                                Màu sắc : <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="<?php echo e($i <= $colorRating ? 'text-warning' : 'text-light'); ?>"
                                                        viewBox="0 0 24 24"
                                                        style="width:20px;height:20px;fill:currentColor;">
                                                        <path
                                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                    </svg>
                                                <?php endfor; ?>
                                            </td>

                                            
                                            <td>
                                                <?php if($review->status === 'public'): ?>
                                                    <span class="badge bg-success w-75">Public</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger w-75">Hidden</span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td>
                                                <?php echo e($review->created_at->format('d/m/Y H:i')); ?>

                                            </td>

                                            
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <div class="edit">
                                                        <form
                                                            action="<?php echo e(route('admin.reviews.toggleStatus', $review->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>

                                                            <button class="btn btn-sm btn-warning"
                                                                title="<?php echo e($review->status === 'public' ? 'Ẩn đánh giá' : 'Hiển thị đánh giá'); ?>">
                                                                <?php if($review->status === 'public'): ?>
                                                                    
                                                                    <i class="ri-eye-off-line"></i>
                                                                <?php else: ?>
                                                                    
                                                                    <i class="ri-eye-line"></i>
                                                                <?php endif; ?>
                                                            </button>
                                                        </form>

                                                    </div>
                                                    <div class="show">
                                                        <button class="btn btn-sm btn-info show-item-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewDetailModal<?php echo e($review->id); ?>">
                                                            <i class="las la-eye"></i>
                                                        </button>
                                                    </div>





                                            </td>

                                        </tr>
                                        <!-- Modal Chi tiết đánh giá -->
                                        <div class="modal fade" id="reviewDetailModal<?php echo e($review->id); ?>"
                                            tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chi tiết đánh giá của người dùng:
                                                            <?php echo e($review->user->name); ?></h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <!-- Người dùng -->
                                                        <div class="mb-3">
                                                            <strong>Người đánh giá:</strong>
                                                            <?php echo e($review->user->name ?? 'Ẩn danh'); ?>

                                                        </div>

                                                        <!-- Sản phẩm -->
                                                        <div class="mb-3">
                                                            <strong>Sản phẩm:</strong>
                                                            <?php echo e($review->product->name); ?>

                                                            <br>
                                                            <small>
                                                                Phân loại hàng:
                                                                <?php echo e($review->productVariant->attribute_summary ?? '---'); ?>

                                                            </small>
                                                        </div>

                                                        <!-- Số sao -->
                                                        <div class="mb-3">
                                                            <strong>Số sao:</strong>
                                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                                <svg class="<?php echo e($i <= $review->rating ? 'text-warning' : 'text-light'); ?>"
                                                                    viewBox="0 0 24 24"
                                                                    style="width:20px;height:20px;fill:currentColor;">
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                    9.24l-7.19-.61L12 2 9.19 8.63
                                    2 9.24l5.46 4.73L5.82 21z" />
                                                                </svg>
                                                            <?php endfor; ?>
                                                        </div>

                                                        <!-- Trải nghiệm -->
                                                        <div class="mb-3">
                                                            <strong>Trải nghiệm:</strong> <br>

                                                            <div>
                                                                <strong>• Chất liệu vải:</strong>
                                                                <?php
                                                                    $fabric = $review->experiences
                                                                        ->where('criterion', 'Chất liệu vải')
                                                                        ->first();
                                                                ?>
                                                                <?php if($fabric): ?>
                                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                                        <svg class="<?php echo e($i <= $fabric->rating ? 'text-warning' : 'text-light'); ?>"
                                                                            viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                            9.24l-7.19-.61L12 2 9.19 8.63
                                            2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php endfor; ?>
                                                                <?php else: ?>
                                                                    Không có
                                                                <?php endif; ?>
                                                            </div>

                                                            <div>
                                                                <strong>• Độ vừa vặn:</strong>
                                                                <?php
                                                                    $fit = $review->experiences
                                                                        ->where('criterion', 'Độ vừa vặn')
                                                                        ->first();
                                                                ?>
                                                                <?php if($fit): ?>
                                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                                        <svg class="<?php echo e($i <= $fit->rating ? 'text-warning' : 'text-light'); ?>"
                                                                            viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                            9.24l-7.19-.61L12 2 9.19 8.63
                                            2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php endfor; ?>
                                                                <?php else: ?>
                                                                    Không có
                                                                <?php endif; ?>
                                                            </div>

                                                            <div>
                                                                <strong>• Màu sắc:</strong>
                                                                <?php
                                                                    $color = $review->experiences
                                                                        ->where('criterion', 'Màu sắc')
                                                                        ->first();
                                                                ?>
                                                                <?php if($color): ?>
                                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                                        <svg class="<?php echo e($i <= $color->rating ? 'text-warning' : 'text-light'); ?>"
                                                                            viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                            9.24l-7.19-.61L12 2 9.19 8.63
                                            2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php endfor; ?>
                                                                <?php else: ?>
                                                                    Không có
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Nội dung -->
                                                        <div class="mb-3">
                                                            <strong>Nội dung:</strong> <br>
                                                            <div class="p-2 bg-light rounded">
                                                                <?php echo e($review->content ?? 'Không có nội dung'); ?>

                                                            </div>
                                                        </div>
                                                        <!-- Ảnh đính kèm -->
                                                        <div class="mb-3">
                                                            <strong>Ảnh đính kèm:</strong> <br>

                                                            <?php if($review->media->count()): ?>
                                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                                    <?php $__currentLoopData = $review->media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <a href="<?php echo e($media->url); ?>" target="_blank">
                                                                            <img src="<?php echo e(Storage::url($media->url)); ?>"
                                                                                alt="Ảnh đánh giá"
                                                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                                                        </a>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="p-2 bg-light rounded">
                                                                    Không có ảnh đính kèm
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Trạng thái -->
                                                        <div class="mb-3">
                                                            <strong>Trạng thái:</strong>
                                                            <?php if($review->status === 'public'): ?>
                                                                <span class="badge bg-success">Public</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Hidden</span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Ngày gửi -->
                                                        <div class="mb-3">
                                                            <strong>Ngày gửi:</strong>
                                                            <?php echo e($review->created_at->format('d/m/Y H:i')); ?>

                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                         <form
                                                            action="<?php echo e(route('admin.reviews.toggleStatus', $review->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>

                                                            <button class="btn btn-warning"
                                                                >
                                                                <?php if($review->status === 'public'): ?>
                                                                    Ẩn đánh giá
                                                                <?php else: ?>
                                                                    Hiển thị đánh giá
                                                                <?php endif; ?>
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>

                            </table>

                            <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                        colors="primary:#121331,secondary:#08a88a"
                                        style="width:75px;height:75px"></lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any
                                        orders for you search.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">

                                
                                <?php if($reviews->onFirstPage()): ?>
                                    <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                                <?php else: ?>
                                    <a class="page-item pagination-prev"
                                        href="<?php echo e($reviews->previousPageUrl()); ?>">Previous</a>
                                <?php endif; ?>

                                
                                <ul class="pagination listjs-pagination mb-0">
                                    <?php $__currentLoopData = $reviews->getUrlRange(1, $reviews->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="page-item <?php echo e($page == $reviews->currentPage() ? 'active' : ''); ?>">
                                            <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>

                                
                                <?php if($reviews->hasMorePages()): ?>
                                    <a class="page-item pagination-next" href="<?php echo e($reviews->nextPageUrl()); ?>">Next</a>
                                <?php else: ?>
                                    <a class="page-item pagination-next disabled" href="javascript:void(0);">Next</a>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end col -->
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
     <script>
        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/reviews/index.blade.php ENDPATH**/ ?>