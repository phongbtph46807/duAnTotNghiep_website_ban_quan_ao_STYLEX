<!DOCTYPE html>
<html lang="vi">
<head>
	<title><?php echo $__env->yieldContent('title', env('APP_NAME')); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<!-- Google Fonts - Hỗ trợ tiếng Việt -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<?php echo $__env->make('client.partials.css.css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="animsition">
	
	<!-- Header -->
	<header class="header-v4">
		<!-- Header menu desktop -->
        <?php echo $__env->make('client.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- mobile reponsive -->
         <?php echo $__env->make('client.partials.mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
		
	</header>

	<!-- Cart -->
    <?php echo $__env->make('client.partials.cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

	<!-- Chat Box -->
    <?php echo $__env->make('client.partials.chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

	<!-- Content -->
	<?php echo $__env->yieldContent('content'); ?>

	<!-- Footer -->
    <?php echo $__env->make('client.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--===============================================================================================-->	
<?php echo $__env->make('client.partials.js.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<!--===============================================================================================-->
<?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX-main\resources\views/client/layouts/app.blade.php ENDPATH**/ ?>