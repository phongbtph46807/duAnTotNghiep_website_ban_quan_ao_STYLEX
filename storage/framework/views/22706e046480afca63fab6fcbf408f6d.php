<?php $__env->startComponent('mail::message'); ?>
# Xin chào <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Vai trò của bạn trong hệ thống đã được cập nhật:

- **Trước đây:** <?php echo new \Illuminate\Support\EncodedHtmlString($oldRole); ?>

- **Hiện tại:** <?php echo new \Illuminate\Support\EncodedHtmlString($newRole); ?>


Vui lòng đăng nhập lại để hệ thống cập nhật quyền truy cập của bạn.

<?php $__env->startComponent('mail::button', ['url' => config('app.url')]); ?>
Trang chủ
<?php echo $__env->renderComponent(); ?>

Trân trọng,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>

<?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/mails/role_changed.blade.php ENDPATH**/ ?>