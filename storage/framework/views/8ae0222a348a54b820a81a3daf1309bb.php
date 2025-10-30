<?php $__env->startSection('title', 'Danh sách người dùng'); ?>
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
                <h4 class="mb-sm-0">Quản lí người dùng</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Quản lí người dùng</a></li>
                        <li class="breadcrumb-item">Danh sách người dùng</li>
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
                        <i class="ri-user-3-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng số người dùng</h5>
                    <h3 class="card-text fw-bold"><?php echo e($userCounts->total_users ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card approved-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-success">
                        <i class="ri-user-follow-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số người dùng hoạt động</h5>
                    <h3 class="card-text fw-bold text-success"><?php echo e($userCounts->active_users ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card pending-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-warning">
                        <i class="ri-user-unfollow-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số người dùng không hoạt động</h5>
                    <h3 class="card-text fw-bold text-warning"><?php echo e($userCounts->inactive_users ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stats-card rejected-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-danger">
                        <i class="ri-lock-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Số người dùng bị khóa</h5>
                    <h3 class="card-text fw-bold text-danger"><?php echo e($userCounts->blocked_users ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách người dùng</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div><!-- end card header -->

                
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="<?php echo e(route('admin.users.index')); ?>" method="GET">
                        <div class="row g-3">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tên</label>
                                <input type="text" name="name" value="<?php echo e(request('name')); ?>" class="form-control"
                                    placeholder="Nhập tên người dùng">
                            </div>

                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="text" name="email" value="<?php echo e(request('email')); ?>" class="form-control"
                                    placeholder="Nhập email">
                            </div>

                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" name="phone_number" value="<?php echo e(request('phone_number')); ?>"
                                    class="form-control" placeholder="Nhập số điện thoại">
                            </div>

                            
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Hoạt động
                                    </option>
                                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Ngừng
                                        hoạt động</option>
                                    <option value="blocked" <?php echo e(request('status') == 'blocked' ? 'selected' : ''); ?>>Bị Khóa</option>
                                </select>
                            </div>

                            
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Vai trò</label>
                                <select name="is_admin" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="1" <?php echo e(request('is_admin') == 1 ? 'selected' : ''); ?>>Quản trị viên
                                    </option>
                                    <option value="0" <?php echo e(request('is_admin') == 0 ? 'selected' : ''); ?>>Người dùng
                                    </option>
                                </select>
                            </div>

                            
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="listjs-table" id="customerList">
                        <div class="row g-4 mb-3">
                            <div class="col-sm-auto">
                                <div>
                                    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-success add-btn"><i
                                            class="ri-add-line align-bottom me-1"></i> Thêm mới</a>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="d-flex justify-content-sm-end">
                                    <div class="search-box ms-2">
                                        <input type="text" class="form-control search" placeholder="Search...">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle table-nowrap" id="customerTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>STT</th>
                                        <th data-sort="customer_name">Tên người dùng</th>
                                        <th data-sort="email">Ảnh</th>
                                        <th data-sort="cate">Email</th>
                                        <th data-sort="phone">Số điện thoại</th>
                                        <th data-sort="phone">Xác minh Email</th>
                                        <th data-sort="phone">Vai trò</th>
                                        <th data-sort="date">Trạng thái</th>
                                        <th data-sort="phone">Ngày tham gia</th>
                                        <th data-sort="action">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php $stt = ($items->currentPage() - 1) * $items->perPage(); ?>
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e(++$stt); ?></td>
                                            <td class="customer_name"><?php echo e($item->name); ?></td>
                                            <td class="email">
                                                <img src="<?php echo e($item->avatar ? asset('storage/' . $item->avatar) : \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT); ?>"
                                                    width="50" height="50" class="user-avatar" alt="Avatar">
                                            </td>
                                            <td class="customer_name"><?php echo e($item->email); ?></td>
                                            <td class="phone"><?php echo e($item->phone_number ?? 'Chưa có thông tin'); ?></td>
                                            <td>
                                                <?php if($item->email_verified_at != null): ?>
                                                    <span class="badge bg-success">
                                                        <i class="ri-check-line me-1"></i>Đã xác minh
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="ri-close-line me-1"></i>Chưa xác minh
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="phone"><?php echo e($item->is_admin ? 'Quản trị viên' : 'Người dùng'); ?></td>
                                            <td class="status">
                                                <?php if($item->status == 'active'): ?>
                                                    <span
                                                        class="badge bg-success-subtle text-success text-uppercase">Active</span>
                                                <?php elseif($item->status == 'inactive'): ?>
                                                    <span
                                                        class="badge bg-warning-subtle text-warning text-uppercase">Inactive</span>
                                                <?php else: ?>
                                                    <span
                                                        class="badge bg-warning-subtle text-warning text-uppercase">Block</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo e(optional($item->created_at)->format('d/m/Y') ?? 'NULL'); ?>

                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <div class="edit">
                                                        <form action="<?php echo e(route('admin.users.edit', $item->id)); ?>"
                                                            method="get">
                                                            <?php echo csrf_field(); ?>
                                                            <button class="btn btn-sm btn-warning edit-item-btn">
                                                                <span class="ri-edit-box-line"></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="show">
                                                        <form action="<?php echo e(route('admin.users.show', $item->id)); ?>"
                                                            method="get">
                                                            <?php echo csrf_field(); ?>
                                                            <button class="btn btn-sm btn-info show-item-btn"
                                                                data-bs-toggle="modal" data-bs-target="#showModal">
                                                                <i class="las la-eye"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="remove">
                                                        <form method="POST"
                                                            action="<?php echo e(route('admin.users.destroy', $item->id)); ?>"
                                                            class="d-inline delete-form">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-item-btn btn-delete"
                                                                data-name="<?php echo e($item->name); ?>">
                                                                <span class="ri-delete-bin-7-line"></span>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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

                                
                                <?php if($items->onFirstPage()): ?>
                                    <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                                <?php else: ?>
                                    <a class="page-item pagination-prev"
                                        href="<?php echo e($items->previousPageUrl()); ?>">Previous</a>
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
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end col -->
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        // Đã bỏ chức năng thay đổi trạng thái xác minh email
    </script>
    <script>
        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/users/index.blade.php ENDPATH**/ ?>