<?php $__env->startSection('title', 'Hồ sơ cá nhân - ' . env('APP_NAME')); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('client.partials.profile-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="container p-t-40 p-b-60">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card profile-card profile-sidebar">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-avatar-placeholder">
                                <span style="font-size: 48px; color: white; font-weight: bold;">
                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-2" style="font-weight: 600; color: #333;"><?php echo e($user->name); ?></h5>
                    <p class="text-muted mb-4" style="font-size: 14px; word-break: break-word;"><?php echo e($user->email); ?></p>
                </div>
            </div>

            <!-- Settings Menu -->
            <div class="card profile-card mt-3">
                <div class="card-body p-0">
                    <div style="padding: 16px; border-bottom: 1px solid #e0e0e0;">
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 15px;">
                            <i class="ri-settings-3-line me-2" style="color: #6777ef;"></i>Cài đặt & Quản lý
                        </h6>
                    </div>
                    <div style="padding: 8px;">
                        <a href="<?php echo e(route('client.profile.index')); ?>" class="settings-menu-item-sidebar active" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-user-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Hồ sơ cá nhân</span>
                        </a>
                        <a href="<?php echo e(route('client.order.list')); ?>" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shopping-bag-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đơn hàng của tôi</span>
                        </a>
                        <a href="<?php echo e(route('client.profile.addresses.index')); ?>" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-map-pin-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Địa chỉ giao hàng</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-bank-card-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Phương thức thanh toán</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-notification-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Cài đặt thông báo</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shield-check-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Bảo mật tài khoản</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-star-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đánh giá của tôi</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h4 class="mb-0" style="font-weight: 600; color: #333;">
                        <i class="ri-user-settings-line me-2" style="color: #6777ef;"></i>Thông tin cá nhân
                    </h4>
                </div>
                <div class="profile-card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('client.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Avatar Section -->
                        <div class="form-section">
                            <div class="avatar-upload-section">
                                <label class="form-label mb-3" style="font-weight: 600; font-size: 16px; color: #333;">
                                    <i class="ri-image-line me-2" style="color: #6777ef;"></i>Ảnh đại diện
                                </label>
                                <div>
                                    <?php if($user->avatar): ?>
                                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" 
                                             id="avatarPreview"
                                             class="avatar-preview-large"
                                             onclick="document.getElementById('avatar').click();">
                                    <?php else: ?>
                                        <div id="avatarPreview" 
                                             class="avatar-placeholder-large"
                                             onclick="document.getElementById('avatar').click();">
                                            <span style="font-size: 60px; color: white; font-weight: bold;">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                                <button type="button" class="btn btn-outline-primary" style="border-radius: 8px; padding: 8px 20px;" onclick="document.getElementById('avatar').click();">
                                    <i class="ri-camera-line me-1"></i> Chọn ảnh mới
                                </button>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="ri-user-line me-2" style="color: #6777ef;"></i>Thông tin cá nhân
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" 
                                           required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
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

                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" 
                                           required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
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

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="phone_number" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Số điện thoại
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="phone_number" name="phone_number" value="<?php echo e(old('phone_number', $user->phone_number)); ?>"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    <?php $__errorArgs = ['phone_number'];
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

                        <!-- Password Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="ri-lock-password-line me-2" style="color: #6777ef;"></i>Đổi mật khẩu
                            </h5>
                            <p class="text-muted mb-4" style="font-size: 14px; margin-bottom: 20px;">
                                <i class="ri-information-line me-1"></i>Để trống nếu không muốn đổi mật khẩu
                            </p>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="current_password" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Mật khẩu hiện tại
                                    </label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="current_password" name="current_password"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    <?php $__errorArgs = ['current_password'];
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

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Mật khẩu mới
                                    </label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password" name="password"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
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

                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Xác nhận mật khẩu mới
                                    </label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 2px solid #f0f0f0;">
                            <a href="<?php echo e(route('home')); ?>" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600; margin-right: 10px;">
                                <i class="ri-arrow-left-line me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="ri-save-line me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Nếu là div, thay thế bằng img
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.className = 'avatar-preview-large';
                img.onclick = function() { document.getElementById('avatar').click(); };
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('client.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\client\profile\index.blade.php ENDPATH**/ ?>