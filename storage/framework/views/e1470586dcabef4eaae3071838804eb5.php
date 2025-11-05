
<?php $__env->startSection('title', 'Quản lý đơn hàng'); ?>

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
    </style>
    <style>
        #filterForm {
            position: relative;
            z-index: 10;
            background-color: #fff;
            /* tránh trong suốt */
            border-top: 1px solid #dee2e6;
            margin-top: 5px;
            padding: 15px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý đơn hàng</h4>
            </div>
        </div>
    </div>

    
    <div class="row cursor-pointer">
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-file-list-line text-primary stat-icon"></i>
                    <h6 class="text-muted">Tổng đơn hàng</h6>
                    <h3><?php echo e($orderStats->total_orders ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-time-line text-warning stat-icon"></i>
                    <h6 class="text-muted">Đang xử lý</h6>
                    <h3><?php echo e($orderStats->processing_orders ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-checkbox-circle-line text-success stat-icon"></i>
                    <h6 class="text-muted">Hoàn tất</h6>
                    <h3><?php echo e($orderStats->completed_orders ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-close-circle-line text-danger stat-icon"></i>
                    <h6 class="text-muted">Đã hủy</h6>
                    <h3><?php echo e($orderStats->cancelled_orders ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Danh sách đơn hàng</h4>
            <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                <i class="ri-filter-3-line"></i> Bộ lọc
            </button>
        </div>

        
        <div class="card-body" id="filterForm" style="display: none;">
            <form action="<?php echo e(route('admin.orders.index')); ?>" method="GET">
                <div class="row g-3">
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Mã đơn hàng</label>
                        <input type="text" name="code" value="<?php echo e(request('code')); ?>" class="form-control"
                            placeholder="Nhập mã đơn hàng...">
                    </div>

                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tên khách hàng</label>
                        <input type="text" name="full_name" value="<?php echo e(request('full_name')); ?>" class="form-control"
                            placeholder="Nhập tên khách hàng...">
                    </div>

                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái đơn hàng</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Chờ xử lý
                            </option>
                            <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Đang xử lý
                            </option>
                            <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Hoàn thành
                            </option>
                            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Đã hủy
                            </option>
                            <option value="refunded" <?php echo e(request('status') == 'refunded' ? 'selected' : ''); ?>>Đã hoàn tiền
                            </option>
                        </select>
                    </div>

                    
                    <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ri-search-line"></i> Lọc
                        </button>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-secondary btn-sm">
                            <i class="ri-refresh-line"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>


        
        <div class="card-body">
            <div class="listjs-table" id="orderList">
                <div class="table-responsive table-card mt-3 mb-1">
                    <table class="table align-middle text-center table-nowrap" id="orderTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn</th>
                                <th>Tên KH</th>
                                <th>Email</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($order->id); ?></td>
                                    <td><?php echo e($order->code); ?></td>
                                    <td><?php echo e($order->full_name); ?></td>
                                    <td><?php echo e($order->email); ?></td>
                                    <td><?php echo e(number_format($order->total)); ?>₫</td>
                                    <td>
                                        <span
                                            class="badge <?php echo e($order->payment_status == 'paid' ? 'bg-success-subtle text-success' : ($order->payment_status == 'unpaid' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary')); ?>">
                                            <?php echo e(ucfirst($order->payment_status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm"
                                            onchange="updateStatus(<?php echo e($order->id); ?>, this.value)">
                                            <?php $__currentLoopData = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn tất', 'cancelled' => 'Đã hủy']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"
                                                    <?php echo e($order->status == $key ? 'selected' : ''); ?>><?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td><?php echo e($order->updated_at->format('d/m/Y H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <?php echo e($orders->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function updateStatus(orderId, status) {
            const url = "<?php echo e(route('admin.orders.updateStatus', ':id')); ?>".replace(':id', orderId);
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    },
                    body: JSON.stringify({
                        status
                    })
                })
                .then(res => res.json())
                .then(data => toastr.success(data.message))
                .catch(err => {
                    toastr.error('Lỗi khi cập nhật trạng thái đơn hàng!');
                    console.error(err);
                });
        }

        
    </script>
    <script>
        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>