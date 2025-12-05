<?php $__env->startSection('title', 'Sửa tài khoản có quyền'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Sửa tài khoản có quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.roles.index')); ?>">Quản lý tài khoản có quyền</a></li>
                        <li class="breadcrumb-item active">Sửa tài khoản</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form sửa tài khoản -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.roles.update', $user->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password" name="password" placeholder="Để trống nếu không muốn đổi mật khẩu">
                                    <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    <?php
                                        $adminCount = \App\Models\User::where('role', 1)->count();
                                        $isLastAdmin = $user->role == 1 && $adminCount <= 1;
                                    ?>
                                    <?php if($isLastAdmin): ?>
                                        <div class="alert alert-warning mb-2">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Lưu ý:</strong> Đây là admin cuối cùng, không thể thay đổi vai trò!
                                        </div>
                                    <?php endif; ?>
                                    <?php if($roles && $roles->count() > 0): ?>
                                        <div class="d-flex flex-wrap gap-3">
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check">
                                                    <input class="form-check-input <?php $__errorArgs = ['role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                           type="checkbox" 
                                                           name="role_ids[]" 
                                                           id="role_<?php echo e($role->id); ?>" 
                                                           value="<?php echo e($role->id); ?>"
                                                           <?php echo e(in_array($role->id, old('role_ids', $userRoles ?? [])) ? 'checked' : ''); ?>

                                                           <?php echo e($isLastAdmin && strtolower($role->name) === 'admin' ? '' : ($isLastAdmin ? 'disabled' : '')); ?>>
                                                    <label class="form-check-label <?php echo e($isLastAdmin && strtolower($role->name) !== 'admin' ? 'text-muted' : ''); ?>" for="role_<?php echo e($role->id); ?>">
                                                        <i class="ri-shield-user-line me-1"></i><?php echo e($role->name); ?>

                                                        <?php if($role->description): ?>
                                                            <small class="text-muted d-block"><?php echo e($role->description); ?></small>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div class="form-text">Có thể chọn nhiều vai trò cho tài khoản này</div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="ri-alert-line me-2"></i>
                                            Chưa có vai trò nào trong hệ thống. 
                                            <a href="<?php echo e(route('admin.rbac.roles.create')); ?>" class="alert-link">Tạo vai trò mới</a>
                                        </div>
                                    <?php endif; ?>
                                    <?php $__errorArgs = ['role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <?php if($isLastAdmin): ?>
                                        <div class="alert alert-warning mb-2">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Lưu ý:</strong> Admin cuối cùng phải ở trạng thái hoạt động!
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   type="radio" name="status" id="status_active" value="active" 
                                                   <?php echo e(old('status', $user->status) == 'active' ? 'checked' : ''); ?>>
                                            <label class="form-check-label text-success" for="status_active">
                                                <i class="ri-checkbox-circle-line me-1"></i>Hoạt động
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   type="radio" name="status" id="status_inactive" value="inactive" 
                                                   <?php echo e(old('status', $user->status) == 'inactive' ? 'checked' : ''); ?>

                                                   <?php echo e($isLastAdmin ? 'disabled' : ''); ?>>
                                            <label class="form-check-label text-warning <?php echo e($isLastAdmin ? 'text-muted' : ''); ?>" for="status_inactive">
                                                <i class="ri-pause-circle-line me-1"></i>Tạm dừng
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   type="radio" name="status" id="status_blocked" value="blocked" 
                                                   <?php echo e(old('status', $user->status) == 'blocked' ? 'checked' : ''); ?>

                                                   <?php echo e($isLastAdmin ? 'disabled' : ''); ?>>
                                            <label class="form-check-label text-danger <?php echo e($isLastAdmin ? 'text-muted' : ''); ?>" for="status_blocked">
                                                <i class="ri-lock-line me-1"></i>Bị khóa
                                            </label>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Phần chọn quyền -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="ri-shield-keyhole-line me-1"></i>Phân quyền <span class="text-muted">(Tùy chọn)</span>
                                    </label>
                                    <div class="alert alert-info mb-3">
                                        <i class="ri-information-line me-2"></i>
                                        <small>Chọn các quyền cụ thể cho tài khoản này. Nếu không chọn, tài khoản sẽ chỉ có quyền mặc định theo vai trò.</small>
                                    </div>
                                    <?php if($permissions && $permissions->count() > 0): ?>
                                        <div class="row">
                                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-4 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               id="permission_<?php echo e($permission->id); ?>" 
                                                               value="<?php echo e($permission->id); ?>"
                                                               <?php echo e(in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="permission_<?php echo e($permission->id); ?>">
                                                            <?php echo e($permission->name); ?>

                                                            <?php if($permission->description): ?>
                                                                <small class="text-muted d-block"><?php echo e($permission->description); ?></small>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">
                                                <i class="ri-checkbox-multiple-line me-1"></i>Chọn tất cả
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
                                                <i class="ri-checkbox-blank-line me-1"></i>Bỏ chọn tất cả
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="ri-alert-line me-2"></i>
                                            Chưa có quyền nào trong hệ thống. Vui lòng tạo quyền trước.
                                        </div>
                                    <?php endif; ?>
                                    <?php $__errorArgs = ['permissions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Cập nhật tài khoản
                                    </button>
                                    <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line me-1"></i>Quay lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chọn tất cả permissions
    const selectAllBtn = document.getElementById('selectAllPermissions');
    const deselectAllBtn = document.getElementById('deselectAllPermissions');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\admin\roles\edit.blade.php ENDPATH**/ ?>