<<<<<<< HEAD
<!DOCTYPE html>
=======
﻿<!DOCTYPE html>
>>>>>>> origin
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

<<<<<<< HEAD
=======
	<!-- Chat Box -->
    <?php echo $__env->make('client.partials.chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
>>>>>>> origin

	<!-- Banner -->
	<?php echo $__env->make('client.partials.banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

	<!-- Product -->
<<<<<<< HEAD
	<section class="bg0 p-t-23 p-b-140">
=======
	<section class="bg0 p-t-23 p-b-140" style="position: relative; z-index: 1; overflow: hidden;">
>>>>>>> origin
		<div class="container">
			<div class="p-b-10">
				<h3 class="ltext-103 cl5">
					New Trend
				</h3>
			</div>

			<div class="flex-w flex-sb-m p-b-52">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
<<<<<<< HEAD
					<a href="<?php echo e(route('client.products.index')); ?>" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(!request('category') ? 'how-active1' : ''); ?>" data-filter="*">
=======
					<a href="#" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(!request('category') ? 'how-active1' : ''); ?> home-category-filter" data-filter="*" data-category-id="">
>>>>>>> origin
						Tất Cả Danh Mục
					</a>

					<?php if(isset($categories) && $categories->count() > 0): ?>
						<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<<<<<<< HEAD
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(request('category') == $category->id ? 'how-active1' : ''); ?>" data-filter=".category-<?php echo e($category->id); ?>">
=======
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?php echo e(request('category') == $category->id ? 'how-active1' : ''); ?> home-category-filter" data-filter=".category-<?php echo e($category->id); ?>" data-category-id="<?php echo e($category->id); ?>">
>>>>>>> origin
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
<<<<<<< HEAD
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Tìm kiếm sản phẩm...">
=======
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04" id="homeSearchBtn">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" id="homeSearchInput" placeholder="Tìm kiếm sản phẩm...">
>>>>>>> origin
					</div>	
				</div>

				<?php echo $__env->make('client.partials.filter-product', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>

<<<<<<< HEAD
			<div class="row isotope-grid">
=======
			<div class="row isotope-grid" id="homeProductGrid" data-api-url="<?php echo e(url('/api/products/filter')); ?>" data-per-page="12">
>>>>>>> origin
				<?php if(isset($products) && $products->count() > 0): ?>
					<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item category-<?php echo e($product->category_id); ?>">
						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="<?php echo e($product->default_image_url); ?>" alt="<?php echo e($product->name); ?>">

<<<<<<< HEAD
								<a href="#" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1">
									Xem nhanh
								</a>
=======
                            <a href="<?php echo e(route('home', ['quick_view' => $product->id])); ?>" 
                               class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                Xem nhanh
                            </a>
>>>>>>> origin
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
<<<<<<< HEAD
			<div class="flex-c-m flex-w w-full p-t-45">
=======
			<div class="flex-c-m flex-w w-full p-t-45 p-b-20">
>>>>>>> origin
				<a href="<?php echo e(route('client.products.index')); ?>" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Xem Thêm
				</a>
			</div>
		</div>
	</section>
	
<<<<<<< HEAD
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
=======
	<style>
		/* Đảm bảo section product không tràn xuống footer */
		section.bg0 {
			position: relative;
			z-index: 1;
			margin-bottom: 0;
		}
		
		#homeProductGrid {
			position: relative;
			min-height: 400px;
		}
		
		#homeProductGrid::after {
			content: '';
			display: block;
			clear: both;
			height: 0;
			visibility: hidden;
		}
	</style>
	
	<!-- Content -->
	<?php echo $__env->make('client.products.mini-product', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
>>>>>>> origin

	<!-- Footer -->
    <?php echo $__env->make('client.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<!--===============================================================================================-->	
<?php echo $__env->make('client.partials.js.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<<<<<<< HEAD
=======

<!-- Home Product Filter Script -->
<script>
$(document).ready(function() {
	const $grid = $('#homeProductGrid');
	const $searchInput = $('#homeSearchInput');
	const apiUrl = $grid.data('api-url') || '/api/products/filter';
	const perPage = $grid.data('per-page') || 12;

	const state = {
		categoryId: null,
		sort: 'relevance',
		minPrice: null,
		maxPrice: null,
		textureId: null,
		keyword: '',
		page: 1,
		isLoading: false
	};

	function formatCurrency(amount) {
		if (!amount) return '0đ';
		return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
	}

	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
	}

	function buildParams() {
		const params = {
			page: state.page,
			per_page: perPage,
			sort: state.sort
		};

		if (state.categoryId) {
			params.category_id = state.categoryId;
		}
		if (state.keyword) {
			params.keyword = state.keyword;
		}
		if (state.minPrice !== null && state.minPrice !== '') {
			params.min_price = state.minPrice;
		}
		if (state.maxPrice !== null && state.maxPrice !== '') {
			params.max_price = state.maxPrice;
		}
		if (state.textureId) {
			params.texture_id = state.textureId;
		}

		return params;
	}

	function renderProducts(items) {
		if (!items || !items.length) {
			$grid.html('<div class="col-12 text-center text-muted py-5">Không tìm thấy sản phẩm phù hợp</div>').css({
				'min-height': '200px',
				'padding-bottom': '40px'
			});
			return;
		}

		const cards = items.map(product => {
			const detailUrl = `/products/${product.id}`;
			const quickViewUrl = state.categoryId
				? `/?quick_view=${product.id}&category=${state.categoryId}`
				: `/?quick_view=${product.id}`;

			const hasSale = product.price_sale && Number(product.price_sale) < Number(product.price);
			let priceHtml = `<span class="stext-105 cl3">`;
			if (hasSale) {
				priceHtml += `
					<span class="fw-bold">${formatCurrency(product.price_sale)}</span>
					<span style="text-decoration: line-through; color: red;">${formatCurrency(product.price)}</span>
				`;
			} else {
				priceHtml += formatCurrency(product.price);
			}
			priceHtml += `</span>`;

			const imageUrl = product.default_image_url || product.default_image || '<?php echo e(asset("client/images/product-01.jpg")); ?>';

			return `
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item category-${product.category_id || ''}">
					<div class="block2">
						<div class="block2-pic hov-img0">
							<img src="${imageUrl}" alt="${escapeHtml(product.name ?? '')}">
							<a href="${quickViewUrl}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
								Xem nhanh
							</a>
						</div>
						<div class="block2-txt flex-w flex-t p-t-14">
							<div class="block2-txt-child1 flex-col-l">
								<a href="${detailUrl}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
									${escapeHtml(product.name ?? '')}
								</a>
								${priceHtml}
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
			`;
		}).join('');

		// Đảm bảo giữ cấu trúc row và thêm padding-bottom
		$grid.html(cards).css({
			'min-height': '400px',
			'padding-bottom': '40px'
		});
	}

	function setActiveCategory(categoryId) {
		$('.home-category-filter').removeClass('how-active1');
		if (!categoryId) {
			$('.home-category-filter[data-category-id=""]').addClass('how-active1');
		} else {
			$(`.home-category-filter[data-category-id="${categoryId}"]`).addClass('how-active1');
		}
	}

	function setActiveSort(sort) {
		$('.js-sort-filter').removeClass('filter-link-active');
		$(`.js-sort-filter[data-sort="${sort}"]`).addClass('filter-link-active');
	}

	function setActivePrice(min, max) {
		$('.js-price-filter').removeClass('filter-link-active');
		const selector = `.js-price-filter[data-min="${min ?? ''}"][data-max="${max ?? ''}"]`;
		const $target = $(selector);
		if ($target.length) {
			$target.addClass('filter-link-active');
		}
	}

	function setActiveTexture(textureId) {
		$('.js-texture-filter').removeClass('filter-link-active');
		const id = textureId ?? '';
		const selector = `.js-texture-filter[data-texture-id="${id}"]`;
		const $target = $(selector);
		if ($target.length) {
			$target.addClass('filter-link-active');
		}
	}

	function fetchProducts() {
		if (state.isLoading) return;
		state.isLoading = true;
		$grid.html('<div class="col-12 text-center py-5"><span class="spinner-border text-dark" role="status"></span></div>').css({
			'min-height': '400px',
			'padding-bottom': '40px'
		});

		$.ajax({
			url: apiUrl,
			type: 'GET',
			data: buildParams(),
			success(response) {
				renderProducts(response.data || []);
			},
			error(xhr) {
				$grid.html('<div class="col-12 text-center text-danger py-5">Không thể tải sản phẩm. Vui lòng thử lại sau.</div>').css({
					'min-height': '400px',
					'padding-bottom': '40px'
				});
			},
			complete() {
				state.isLoading = false;
			}
		});
	}

	// Search timeout
	let searchTimeout = null;

	// Category filter
	$(document).on('click', '.home-category-filter', function(e) {
		e.preventDefault();
		const categoryId = $(this).data('category-id');
		state.categoryId = categoryId ? Number(categoryId) : null;
		state.page = 1;
		setActiveCategory(categoryId || '');
		fetchProducts();
	});

	// Sort filter
	$(document).on('click', '.js-sort-filter', function(e) {
		e.preventDefault();
		const sortValue = $(this).data('sort');
		if (!sortValue) return;
		state.sort = sortValue;
		state.page = 1;
		setActiveSort(sortValue);
		fetchProducts();
	});

	// Price filter
	$(document).on('click', '.js-price-filter', function(e) {
		e.preventDefault();
		const min = $(this).data('min');
		const max = $(this).data('max');
		state.minPrice = min !== undefined && min !== '' ? Number(min) : null;
		state.maxPrice = max !== undefined && max !== '' ? Number(max) : null;
		state.page = 1;
		setActivePrice($(this).data('min') ?? '', $(this).data('max') ?? '');
		fetchProducts();
	});

	// Texture filter
	$(document).on('click', '.js-texture-filter', function(e) {
		e.preventDefault();
		const textureId = $(this).data('texture-id');
		state.textureId = textureId ? Number(textureId) : null;
		state.page = 1;
		setActiveTexture(textureId || '');
		fetchProducts();
	});

	// Search
	$searchInput.on('input', function() {
		const value = $(this).val();
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(() => {
			state.keyword = value;
			state.page = 1;
			fetchProducts();
		}, 400);
	});

	$('#homeSearchBtn').on('click', function(e) {
		e.preventDefault();
		const value = $searchInput.val();
		state.keyword = value;
		state.page = 1;
		fetchProducts();
	});

	// Init - giữ nguyên sản phẩm ban đầu từ server nếu chưa có filter nào
});
</script>
>>>>>>> origin
<!--===============================================================================================--><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/duAnTotNghiep_website_ban_quan_ao_STYLEX/resources/views/client/index.blade.php ENDPATH**/ ?>