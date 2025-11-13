<?php $__env->startSection('title', 'Danh sách phần thưởng Spin'); ?>

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
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lí Spin</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Quản lí Spin</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.spins.index')); ?>">Danh sách phần thưởng</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row cursor-pointer">
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card total-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-primary">
                        <i class="ri-gift-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng phần thưởng</h5>
                    <h3 class="card-text fw-bold"><?php echo e($spinCounts->total_spins ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card approved-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Đang hoạt động</h5>
                    <h3 class="card-text fw-bold text-success"><?php echo e($spinCounts->active_spins ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card pending-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-warning">
                        <i class="ri-pause-circle-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Không hoạt động</h5>
                    <h3 class="card-text fw-bold text-warning"><?php echo e($spinCounts->inactive_spins ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card rejected-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-info">
                        <i class="ri-trophy-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng lượt quay</h5>
                    <h3 class="card-text fw-bold text-info"><?php echo e($spinCounts->total_spun ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách phần thưởng Spin</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div>

                
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="<?php echo e(route('admin.spins.index')); ?>" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tên phần thưởng</label>
                                <input type="text" name="name" value="<?php echo e(request('name')); ?>" class="form-control" placeholder="Nhập tên">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Hoạt động</option>
                                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Ngừng hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('admin.spins.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row g-4 mb-3">
                        <div class="col-sm-auto">
                            <a href="<?php echo e(route('admin.spins.create')); ?>" class="btn btn-success add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                            </a>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form method="GET" action="<?php echo e(route('admin.spins.index')); ?>" class="d-flex align-items-center" style="max-width: 320px;">
                                <div class="input-group">
                                    <input type="text" name="name" value="<?php echo e(request('name')); ?>" class="form-control" placeholder="Tìm kiếm...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle text-center table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên phần thưởng</th>
                                <th>Voucher</th>
                                <th>Xác suất (%)</th>
                                <th>Lượt quay</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->name); ?></td>
                                    <td>
                                        <?php if($item->voucher): ?>
                                            <span class="badge bg-primary"><?php echo e($item->voucher->code); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($item->probability); ?>%</td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($item->spinUsers->count()); ?></span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch form-switch-success d-inline-block">
                                            <input class="form-check-input" type="checkbox" <?php if($item->is_active): echo 'checked'; endif; ?>
                                            onchange="toggleStatus(<?php echo e($item->id); ?>, this.checked)">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="<?php echo e(route('admin.spins.edit', $item->id)); ?>" class="btn btn-sm btn-warning">
                                                <i class="ri-edit-box-line"></i>
                                            </a>



                                            <form method="POST" action="<?php echo e(route('admin.spins.destroy', $item->id)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-name="<?php echo e($item->name); ?>">
                                                    <i class="ri-delete-bin-7-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            <?php if($items->onFirstPage()): ?>
                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                            <?php else: ?>
                                <a class="page-item pagination-prev" href="<?php echo e($items->previousPageUrl()); ?>">Previous</a>
                            <?php endif; ?>

                            <ul class="pagination listjs-pagination mb-0">
                                <?php $__currentLoopData = $items->getUrlRange(1, $items->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="page-item <?php echo e($page == $items->currentPage() ? 'active' : ''); ?>">
                                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <?php if($items->hasMorePages()): ?>
                                <a class="page-item pagination-next" href="<?php echo e($items->nextPageUrl()); ?>">Next</a>
                            <?php else: ?>
                                <a class="page-item pagination-next disabled" href="javascript:void(0);">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function toggleStatus(spinId, isChecked) {
            const url = "<?php echo e(route('admin.spins.toggleStatus', ':id')); ?>".replace(':id', spinId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({ is_active: isChecked ? 1 : 0 })
            })
                .then(res => res.json())
                .then(data => toastr.success(data.message))
                .catch(err => toastr.error('Lỗi cập nhật trạng thái!'));
        }

        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/spins/index.blade.php ENDPATH**/ ?>