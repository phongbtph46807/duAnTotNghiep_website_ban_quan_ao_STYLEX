<?php $__env->startSection('title', 'Thông tin cá nhân'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thông tin cá nhân</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <img src="<?php echo e($user->avatar ? asset('storage/' . $user->avatar) : \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT); ?>" 
                                 alt="Avatar" class="rounded-circle" width="150" height="150">
                        </div>
                        <h5><?php echo e($user->name); ?></h5>
                        <p class="text-muted"><?php echo e($user->email); ?></p>
                        <?php if($user->role == 1): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php elseif($user->role == 2): ?>
                            <span class="badge bg-warning">Staff</span>
                        <?php else: ?>
                            <span class="badge bg-info">User</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td><?php echo e($user->name); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo e($user->email); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td><?php echo e($user->phone_number ?? 'Chưa cập nhật'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Vai trò:</strong></td>
                                    <td>
                                        <?php if($user->role == 1): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php elseif($user->role == 2): ?>
                                            <span class="badge bg-warning">Staff</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <?php if($user->status == 'active'): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php elseif($user->status == 'inactive'): ?>
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Bị khóa</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td><?php echo e($user->created_at->format('d/m/Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật lần cuối:</strong></td>
                                    <td><?php echo e($user->updated_at->format('d/m/Y H:i')); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="<?php echo e(route('admin.profile.edit')); ?>" class="btn btn-primary">
                                <i class="ri-edit-line me-1"></i> Chỉnh sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/users/profile.blade.php ENDPATH**/ ?>