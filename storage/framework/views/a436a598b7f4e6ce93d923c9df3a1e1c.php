<?php $__env->startSection('title', 'Thêm địa chỉ mới - ' . env('APP_NAME')); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('client.partials.profile-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="container p-t-40 p-b-60">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card profile-card profile-sidebar">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <?php if(auth()->user()->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="Avatar" class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-avatar-placeholder">
                                <span style="font-size: 48px; color: white; font-weight: bold;">
                                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-2" style="font-weight: 600; color: #333;"><?php echo e(auth()->user()->name); ?></h5>
                    <p class="text-muted mb-4" style="font-size: 14px; word-break: break-word;"><?php echo e(auth()->user()->email); ?></p>
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
                        <a href="<?php echo e(route('client.profile.index')); ?>" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-user-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Hồ sơ cá nhân</span>
                        </a>
                        <a href="<?php echo e(route('client.order.list')); ?>" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shopping-bag-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đơn hàng của tôi</span>
                        </a>
                        <a href="<?php echo e(route('client.profile.addresses.index')); ?>" class="settings-menu-item-sidebar active" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
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
                        <i class="ri-add-line me-2" style="color: #6777ef;"></i>Thêm địa chỉ mới
                    </h4>
                </div>
                <div class="profile-card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('client.profile.addresses.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="full_name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Họ và tên người nhận <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="full_name" name="full_name" value="<?php echo e(old('full_name', auth()->user()->name)); ?>" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                <?php $__errorArgs = ['full_name'];
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
                                <label for="phone" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="phone" name="phone" value="<?php echo e(old('phone', auth()->user()->phone_number)); ?>" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                <?php $__errorArgs = ['phone'];
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
                                <label for="email" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Email
                                </label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="email" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
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

                            <div class="col-md-6 mb-4">
                                <label for="address_type" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Loại địa chỉ <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?php $__errorArgs = ['address_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="address_type" name="address_type" required
                                        style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    <option value="home" <?php echo e(old('address_type') == 'home' ? 'selected' : ''); ?>>Nhà riêng</option>
                                    <option value="office" <?php echo e(old('address_type') == 'office' ? 'selected' : ''); ?>>Văn phòng</option>
                                    <option value="other" <?php echo e(old('address_type') == 'other' ? 'selected' : ''); ?>>Khác</option>
                                </select>
                                <?php $__errorArgs = ['address_type'];
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
                            <div class="col-md-4 mb-4">
                                <label for="city" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Tỉnh/Thành phố <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="city" name="city" value="<?php echo e(old('city')); ?>" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                <?php $__errorArgs = ['city'];
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

                            <div class="col-md-4 mb-4">
                                <label for="district" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Quận/Huyện
                                </label>
                                <input type="text" class="form-control <?php $__errorArgs = ['district'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="district" name="district" value="<?php echo e(old('district')); ?>"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                <?php $__errorArgs = ['district'];
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

                            <div class="col-md-4 mb-4">
                                <label for="ward" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Phường/Xã
                                </label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ward'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="ward" name="ward" value="<?php echo e(old('ward')); ?>"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                <?php $__errorArgs = ['ward'];
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
                            <div class="col-md-12 mb-4">
                                <label for="address" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Địa chỉ chi tiết <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="address" name="address" rows="3" required
                                          style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;"><?php echo e(old('address')); ?></textarea>
                                <?php $__errorArgs = ['address'];
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
                            <div class="col-md-12 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                                           <?php echo e(old('is_default') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_default" style="font-weight: 500;">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 2px solid #f0f0f0;">
                            <a href="<?php echo e(route('client.profile.addresses.index')); ?>" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                                <i class="ri-arrow-left-line me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="ri-save-line me-1"></i> Lưu địa chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('client.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX-main\resources\views/client/profile/addresses/create.blade.php ENDPATH**/ ?>