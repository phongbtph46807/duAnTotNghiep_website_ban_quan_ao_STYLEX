<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ env('APP_NAME') }}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@include('client.partials.css.css')
</head>
<body class="animsition">

	<!-- Header -->
	<header class="header-v4">
		<!-- Header menu desktop -->
		@include('client.partials.sidebar')

        <!-- mobile reponsive -->
        @include('client.partials.mobile')

	</header>

	<!-- Cart -->
    @include('client.partials.cart')

	<!-- Chat Box -->
    @include('client.partials.chat')

	<!-- Banner -->
	@include('client.partials.banner')

	<!-- Product -->
	<section class="bg0 p-t-23 p-b-140" style="position: relative; z-index: 1; overflow: hidden;">
		<div class="container">
			<div class="p-b-10">
				<h3 class="ltext-103 cl5">
					New Trend
				</h3>
			</div>

			<div class="flex-w flex-sb-m p-b-52">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
					<a href="#" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ !request('category') ? 'how-active1' : '' }} home-category-filter" data-filter="*" data-category-id="">
						Tất Cả Danh Mục
					</a>

					@if(isset($categories) && $categories->count() > 0)
						@foreach($categories as $index => $category)
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ request('category') == $category->id ? 'how-active1' : '' }} home-category-filter" data-filter=".category-{{ $category->id }}" data-category-id="{{ $category->id }}">
						{{ $category->name }}
					</button>
						@endforeach
					@endif
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
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04" id="homeSearchBtn">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" id="homeSearchInput" placeholder="Tìm kiếm sản phẩm...">
					</div>
				</div>

				@include('client.partials.filter-product')
			</div>

			<div class="row isotope-grid" id="homeProductGrid" data-api-url="{{ url('/api/products/filter') }}" data-per-page="12">
				@if(isset($products) && $products->count() > 0)
					@foreach($products as $product)
					<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item category-{{ $product->category_id }}">
						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="{{ $product->default_image_url }}" alt="{{ $product->name }}">

                            <a href="{{ route('home', ['quick_view' => $product->id]) }}"
                               class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                Xem nhanh
                            </a>
							</div>

							<div class="block2-txt flex-w flex-t p-t-14">
								<div class="block2-txt-child1 flex-col-l ">
									<a href="{{ route('client.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
										{{ $product->name }}
									</a>

									<span class="stext-105 cl3">
										@if($product->price_sale && $product->price_sale < $product->price)
											<span class="fw-bold">{{ number_format($product->price_sale, 0, ',', '.') }}đ</span>
											<span style="text-decoration: line-through; color: red;">{{ number_format($product->price, 0, ',', '.') }}đ</span>
										@else
											{{ number_format($product->price, 0, ',', '.') }}đ
										@endif
									</span>
								</div>

							<div class="block2-txt-child2 flex-r p-t-3">
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2" data-product-id="{{ $product->id }}" style="margin-right: 15px;">
									<img class="icon-heart1 dis-block trans-04" src="{{ asset('client/images/icons/icon-heart-01.png') }}" alt="ICON">
									<img class="icon-heart2 dis-block trans-04 ab-t-l" src="{{ asset('client/images/icons/icon-heart-02.png') }}" alt="ICON">
								</a>
							</div>
							</div>
						</div>
					</div>
					@endforeach
				@else
					<div class="col-12 text-center">
						<p class="text-muted">Chưa có sản phẩm nào</p>
					</div>
				@endif
			</div>


			<!-- Load more -->
			<div class="flex-c-m flex-w w-full p-t-45 p-b-20">
				<a href="{{ route('client.products.index') }}" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Xem Thêm
				</a>
			</div>
		</div>
	</section>

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
	@include('client.products.mini-product')

	<!-- Footer -->
    @include('client.partials.footer')
</body>
</html>
<!--===============================================================================================-->
@include('client.partials.js.js')

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

			const imageUrl = product.default_image_url || product.default_image || '{{ asset("client/images/product-01.jpg") }}';

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
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2" data-product-id="${product.id}" style="margin-right: 15px;">
									<img class="icon-heart1 dis-block trans-04" src="{{ asset('client/images/icons/icon-heart-01.png') }}" alt="ICON">
									<img class="icon-heart2 dis-block trans-04 ab-t-l" src="{{ asset('client/images/icons/icon-heart-02.png') }}" alt="ICON">
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
<!--===============================================================================================-->
