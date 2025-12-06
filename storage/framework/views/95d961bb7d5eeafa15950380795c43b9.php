<?php $__env->startComponent('mail::message'); ?>
# Xin chào <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Tài khoản của bạn đã được **mở khóa** và có thể đăng nhập trở lại bình thường.

<?php $__env->startComponent('mail::button', ['url' => config('app.url') . '/login']); ?>
Đăng nhập ngay
<?php echo $__env->renderComponent(); ?>

Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!  
Trân trọng,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>

<?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/mails/account_unblocked.blade.php ENDPATH**/ ?>