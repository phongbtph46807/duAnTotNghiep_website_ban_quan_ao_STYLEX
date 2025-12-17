<?php $__env->startSection('title'); ?>
    Danh sách kho hàng
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý kho hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sách kho hàng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách kho hàng</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div>

                
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="<?php echo e(route('admin.inventory.warehouses.index')); ?>" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tìm kiếm</label>
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control"
                                    placeholder="Nhập mã hoặc tên kho">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Loại kho</label>
                                <select name="type" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="PHYSICAL" <?php echo e(request('type') == 'PHYSICAL' ? 'selected' : ''); ?>>Kho vật lý</option>
                                    <option value="VIRTUAL" <?php echo e(request('type') == 'VIRTUAL' ? 'selected' : ''); ?>>Kho ảo</option>
                                    <option value="CONSIGNMENT" <?php echo e(request('type') == 'CONSIGNMENT' ? 'selected' : ''); ?>>Kho ký gửi</option>
                                    <option value="SCRAP" <?php echo e(request('type') == 'SCRAP' ? 'selected' : ''); ?>>Kho phế liệu</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="ACTIVE" <?php echo e(request('status') == 'ACTIVE' ? 'selected' : ''); ?>>Đang hoạt động</option>
                                    <option value="INACTIVE" <?php echo e(request('status') == 'INACTIVE' ? 'selected' : ''); ?>>Tạm ngưng</option>
                                    <option value="MAINTENANCE" <?php echo e(request('status') == 'MAINTENANCE' ? 'selected' : ''); ?>>Bảo trì</option>
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('admin.inventory.warehouses.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row g-4 mb-3">
                        <div class="col-sm-auto">
                            <div>
                                <a href="<?php echo e(route('admin.inventory.warehouses.create')); ?>" class="btn btn-success add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                                </a>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="d-flex justify-content-sm-end">
                                <div class="search-box ms-2">
                                    <input type="text" name="search_full" id="searchFull"
                                        class="form-control search" placeholder="Tìm kiếm..." 
                                        value="<?php echo e(request('search')); ?>">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã Kho</th>
                                    <th>Tên Kho</th>
                                    <th>Loại</th>
                                    <th>Trạng Thái</th>
                                    <th>Địa Chỉ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($warehouses->firstItem() + $index); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo e($warehouse->code); ?></span></td>
                                        <td><strong><?php echo e($warehouse->name); ?></strong></td>
                                        <td>
                                            <?php switch($warehouse->type):
                                                case ('PHYSICAL'): ?>
                                                    <span class="badge bg-primary-subtle text-primary">Kho vật lý</span>
                                                    <?php break; ?>
                                                <?php case ('VIRTUAL'): ?>
                                                    <span class="badge bg-info-subtle text-info">Kho ảo</span>
                                                    <?php break; ?>
                                                <?php case ('CONSIGNMENT'): ?>
                                                    <span class="badge bg-warning-subtle text-warning">Kho ký gửi</span>
                                                    <?php break; ?>
                                                <?php case ('SCRAP'): ?>
                                                    <span class="badge bg-dark-subtle text-dark">Kho phế liệu</span>
                                                    <?php break; ?>
                                            <?php endswitch; ?>
                                        </td>
                                        <td>
                                            <?php switch($warehouse->operational_status):
                                                case ('ACTIVE'): ?>
                                                    <span class="badge bg-success-subtle text-success">Đang hoạt động</span>
                                                    <?php break; ?>
                                                <?php case ('INACTIVE'): ?>
                                                    <span class="badge bg-danger-subtle text-danger">Tạm ngưng</span>
                                                    <?php break; ?>
                                                <?php case ('MAINTENANCE'): ?>
                                                    <span class="badge bg-warning-subtle text-warning">Bảo trì</span>
                                                    <?php break; ?>
                                            <?php endswitch; ?>
                                        </td>
                                        <td><?php echo e(Str::limit($warehouse->address ?? '-', 40)); ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <div class="show">
                                                    <form action="<?php echo e(route('admin.inventory.warehouses.show', $warehouse)); ?>" method="get">
                                                        <?php echo csrf_field(); ?>
                                                        <button class="btn btn-sm btn-info show-item-btn">
                                                            <i class="las la-eye"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="edit">
                                                    <form action="<?php echo e(route('admin.inventory.warehouses.edit', $warehouse)); ?>" method="get">
                                                        <?php echo csrf_field(); ?>
                                                        <button class="btn btn-sm btn-warning edit-item-btn">
                                                            <span class="ri-edit-box-line"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="remove">
                                                    <form method="POST" action="<?php echo e(route('admin.inventory.warehouses.destroy', $warehouse)); ?>" class="d-inline delete-form" id="delete-form-<?php echo e($warehouse->id); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-warehouse-id="<?php echo e($warehouse->id); ?>" data-warehouse-name="<?php echo e($warehouse->name); ?>">
                                                            <span class="ri-delete-bin-7-line"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <p class="mb-0">Không tìm thấy kho hàng nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Xin lỗi! Không tìm thấy kết quả</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            <?php if($warehouses->onFirstPage()): ?>
                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                            <?php else: ?>
                                <a class="page-item pagination-prev" href="<?php echo e($warehouses->previousPageUrl()); ?>">Previous</a>
                            <?php endif; ?>

                            <ul class="pagination listjs-pagination mb-0">
                                <?php $__currentLoopData = $warehouses->getUrlRange(1, $warehouses->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="page-item <?php echo e($page == $warehouses->currentPage() ? 'active' : ''); ?>">
                                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <?php if($warehouses->hasMorePages()): ?>
                                <a class="page-item pagination-next" href="<?php echo e($warehouses->nextPageUrl()); ?>">Next</a>
                            <?php else: ?>
                                <a class="page-item pagination-next disabled" href="javascript:void(0);">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Toggle filter
        $('#toggleFilterBtn').on('click', function() {
            $('#filterForm').slideToggle(200);
        });

        // Xử lý xóa kho
        $('.remove-item-btn').on('click', function(e) {
            e.preventDefault();
            var warehouseId = $(this).data('warehouse-id');
            var warehouseName = $(this).data('warehouse-name');
            
            if (confirm('Bạn có chắc chắn muốn xóa kho "' + warehouseName + '"?\n\n' +
                        'Lưu ý:\n' +
                        '- Không thể xóa kho đang có tồn kho!\n' +
                        '- Dữ liệu sẽ bị xóa vĩnh viễn và không thể khôi phục!')) {
                $('#delete-form-' + warehouseId).submit();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/warehouse/index.blade.php ENDPATH**/ ?>