<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo e(env('APP_NAME')); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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


	<!-- Banner -->
	<?php echo $__env->make('client.partials.banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

	<!-- Product -->
	<section class="bg0 p-t-23 p-b-140">
		<div class="container">
			<div class="p-b-10">
				<h3 class="ltext-103 cl5">
					New Trend
				</h3>
			</div>

			<div class="flex-w flex-sb-m p-b-52">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
					<a href="<?php echo e(route('client.products.index')); ?>" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(!request('category') ? 'how-active1' : ''); ?>" data-filter="*">
						Tất Cả Danh Mục
					</a>

					<?php if(isset($categories) && $categories->count() > 0): ?>
						<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(request('category') == $category->id ? 'how-active1' : ''); ?>" data-filter=".category-<?php echo e($category->id); ?>">
						<?php echo e($category->name); ?>

					</button>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endif; ?>
				</div>

				<div class="flex-w flex-c-m m-tb-10">
					<div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
						<i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
						<i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						 Lọc
					</div>

					<div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
						<i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
						<i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						Tìm Kiếm
					</div>
				</div>
				
				<!-- Search product -->
				<div class="dis-none panel-search w-full p-t-10 p-b-15">
					<div class="bor8 dis-flex p-l-15">
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Tìm kiếm sản phẩm...">
					</div>	
				</div>

				<?php echo $__env->make('client.partials.filter-product', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>

			<div class="row isotope-grid">
				<?php if(isset($products) && $products->count() > 0): ?>
					<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item category-<?php echo e($product->category_id); ?>">
						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="<?php echo e($product->default_image_url); ?>" alt="<?php echo e($product->name); ?>">

								<a href="#" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1">
									Xem nhanh
								</a>
							</div>

							<div class="block2-txt flex-w flex-t p-t-14">
								<div class="block2-txt-child1 flex-col-l ">
									<a href="<?php echo e(route('client.products.show', $product->id)); ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
										<?php echo e($product->name); ?>

									</a>

									<span class="stext-105 cl3">
										<?php if($product->price_sale && $product->price_sale < $product->price): ?>
											<span class="fw-bold"><?php echo e(number_format($product->price_sale, 0, ',', '.')); ?>đ</span>
											<span style="text-decoration: line-through; color: red;"><?php echo e(number_format($product->price, 0, ',', '.')); ?>đ</span>
										<?php else: ?>
											<?php echo e(number_format($product->price, 0, ',', '.')); ?>đ
										<?php endif; ?>
									</span>
								</div>

							<div class="block2-txt-child2 flex-r p-t-3">
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2" style="margin-right: 15px;">
									<img class="icon-heart1 dis-block trans-04" src="<?php echo e(asset('client/images/icons/icon-heart-01.png')); ?>" alt="ICON">
									<img class="icon-heart2 dis-block trans-04 ab-t-l" src="<?php echo e(asset('client/images/icons/icon-heart-02.png')); ?>" alt="ICON">
								</a>
							</div>
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php else: ?>
					<div class="col-12 text-center">
						<p class="text-muted">Chưa có sản phẩm nào</p>
					</div>
				<?php endif; ?>
			</div>
			

			<!-- Load more -->
			<div class="flex-c-m flex-w w-full p-t-45">
				<a href="<?php echo e(route('client.products.index')); ?>" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Xem Thêm
				</a>
			</div>
		</div>
	</section>
	
	<!-- Content -->
	<div class="wrap-modal1 js-modal1 p-t-60 p-b-20">
		<div class="overlay-modal1 js-hide-modal1"></div>

		<div class="container">
			<div class="bg0 p-t-60 p-b-30 p-lr-15-lg how-pos3-parent">
				<button class="how-pos3 hov3 trans-04 js-hide-modal1">
					<img src="<?php echo e(asset('client/images/icons/icon-close.png')); ?>" alt="CLOSE">
				</button>

				<div class="row">
					<div class="col-md-6 col-lg-7 p-b-30">
						<div class="p-l-25 p-r-30 p-lr-0-lg">
							<div class="wrap-slick3 flex-sb flex-w">
								<div class="wrap-slick3-dots"></div>
								<div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

								<div class="slick3 gallery-lb">
									<div class="item-slick3" data-thumb="<?php echo e(asset('client/images/product-detail-01.jpg')); ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo e(asset('client/images/product-detail-01.jpg')); ?>" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo e(asset('client/images/product-detail-01.jpg')); ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>

									<div class="item-slick3" data-thumb="<?php echo e(asset('client/images/product-detail-02.jpg')); ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo e(asset('client/images/product-detail-02.jpg')); ?>" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo e(asset('client/images/product-detail-02.jpg')); ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>

									<div class="item-slick3" data-thumb="<?php echo e(asset('client/images/product-detail-03.jpg')); ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo e(asset('client/images/product-detail-03.jpg')); ?>" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo e(asset('client/images/product-detail-03.jpg')); ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-6 col-lg-5 p-b-30">
						<div class="p-r-50 p-t-5 p-lr-0-lg">
							<h4 class="mtext-105 cl2 js-name-detail p-b-14">
								Lightweight Jacket
							</h4>

							<span class="mtext-106 cl2">
								$58.79
							</span>

							<p class="stext-102 cl3 p-t-23">
								Nulla eget sem vitae eros pharetra viverra. Nam vitae luctus ligula. Mauris consequat ornare feugiat.
							</p>
							
							<!--  -->
							<div class="p-t-33">
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Size
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="time">
												<option>Choose an option</option>
												<option>Size S</option>
												<option>Size M</option>
												<option>Size L</option>
												<option>Size XL</option>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>

								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Color
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="time">
												<option>Choose an option</option>
												<option>Red</option>
												<option>Blue</option>
												<option>White</option>
												<option>Grey</option>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>

								<div class="flex-w flex-r-m p-b-10">
									<div class="size-204 flex-w flex-m respon6-next">
										<div class="wrap-num-product flex-w m-r-20 m-tb-10">
											<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-minus"></i>
											</div>

											<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1">

											<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>

										<button class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail">
											Add to cart
										</button>
									</div>
								</div>	
							</div>

							<!--  -->
							<div class="flex-w flex-m p-l-100 p-t-40 respon7">
								<div class="flex-m bor9 p-r-10 m-r-11">
									<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100" data-tooltip="Add to Wishlist">
										<i class="zmdi zmdi-favorite"></i>
									</a>
								</div>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Facebook">
									<i class="fa fa-facebook"></i>
								</a>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Twitter">
									<i class="fa fa-twitter"></i>
								</a>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Google Plus">
									<i class="fa fa-google-plus"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Footer -->
    <?php echo $__env->make('client.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<!--===============================================================================================-->	
<?php echo $__env->make('client.partials.js.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--===============================================================================================--><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\client\index.blade.php ENDPATH**/ ?>