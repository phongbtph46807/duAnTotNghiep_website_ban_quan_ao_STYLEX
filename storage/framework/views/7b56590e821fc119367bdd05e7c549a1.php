<?php $__env->startSection('title', 'Địa chỉ giao hàng - ' . env('APP_NAME')); ?>

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
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0" style="font-weight: 600; color: #333;">
                            <i class="ri-map-pin-line me-2" style="color: #6777ef;"></i>Địa chỉ giao hàng
                        </h4>
                        <a href="<?php echo e(route('client.profile.addresses.create')); ?>" class="btn btn-primary-custom">
                            <i class="ri-add-line me-1"></i> Thêm địa chỉ mới
                        </a>
                    </div>
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

                    <?php if($addresses->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12">
                                    <div class="address-card <?php echo e($address->is_default ? 'default' : ''); ?>">
                                        <div class="address-header">
                                            <div>
                                                <span class="address-badge badge-default">
                                                    <i class="ri-map-pin-fill me-1"></i>
                                                    <?php echo e($address->is_default ? 'Mặc định' : 'Địa chỉ ' . $loop->iteration); ?>

                                                </span>
                                                <span class="address-badge badge-type">
                                                    <?php if($address->address_type === 'home'): ?>
                                                        <i class="ri-home-line me-1"></i>Nhà riêng
                                                    <?php elseif($address->address_type === 'office'): ?>
                                                        <i class="ri-building-line me-1"></i>Văn phòng
                                                    <?php else: ?>
                                                        <i class="ri-map-pin-line me-1"></i>Khác
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="address-actions">
                                                <?php if(!$address->is_default): ?>
                                                    <button type="button" class="btn-action btn-set-default" data-address-id="<?php echo e($address->id); ?>" onclick="setDefault(this.dataset.addressId)">
                                                        <i class="ri-checkbox-circle-line me-1"></i> Đặt mặc định
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?php echo e(route('client.profile.addresses.edit', $address)); ?>" class="btn-action btn-edit" style="text-decoration: none;">
                                                    <i class="ri-edit-line me-1"></i> Sửa
                                                </a>
                                                <button type="button" class="btn-action btn-delete" data-address-id="<?php echo e($address->id); ?>" onclick="deleteAddress(this.dataset.addressId)">
                                                    <i class="ri-delete-bin-line me-1"></i> Xóa
                                                </button>
                                            </div>
                                        </div>
                                        <div style="line-height: 1.8;">
                                            <div style="font-weight: 600; font-size: 16px; margin-bottom: 8px; color: #333;">
                                                <?php echo e($address->full_name); ?>

                                            </div>
                                            <div style="color: #666; margin-bottom: 4px;">
                                                <i class="ri-phone-line me-1"></i><?php echo e($address->phone); ?>

                                            </div>
                                            <?php if($address->email): ?>
                                                <div style="color: #666; margin-bottom: 4px;">
                                                    <i class="ri-mail-line me-1"></i><?php echo e($address->email); ?>

                                                </div>
                                            <?php endif; ?>
                                            <div style="color: #666; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                                                <i class="ri-map-pin-2-line me-1"></i>
                                                <?php echo e($address->full_address); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="ri-map-pin-line" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                            <h5 style="color: #666; margin-bottom: 10px;">Chưa có địa chỉ nào</h5>
                            <p style="color: #999; margin-bottom: 20px;">Thêm địa chỉ để nhận hàng nhanh chóng hơn</p>
                            <a href="<?php echo e(route('client.profile.addresses.create')); ?>" class="btn btn-primary-custom">
                                <i class="ri-add-line me-1"></i> Thêm địa chỉ đầu tiên
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDefault(addressId) {
    if (!confirm('Bạn có chắc chắn muốn đặt địa chỉ này làm mặc định?')) {
        return;
    }

    const url = '<?php echo e(route("client.profile.addresses.set-default", ":id")); ?>'.replace(':id', addressId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    });
}

function deleteAddress(addressId) {
    if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
        return;
    }

    const url = '<?php echo e(route("client.profile.addresses.destroy", ":id")); ?>'.replace(':id', addressId);
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    });
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('client.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\client\profile\addresses\index.blade.php ENDPATH**/ ?>