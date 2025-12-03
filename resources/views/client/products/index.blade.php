@extends('client.layouts.app')

@section('title', 'Sản Phẩm - ' . env('APP_NAME'))

@section('content')
<!-- Product -->
<div class="bg0 m-t-23 p-b-140">
	<div class="container">
		<div class="flex-w flex-sb-m p-b-52">
			<div class="flex-w flex-l-m filter-tope-group m-tb-10">
				<a href="{{ route('client.products.index') }}" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ !request('category') ? 'how-active1' : '' }}" data-filter="*">
					Tất Cả Sản Phẩm
				</a>

				@if(isset($categories) && $categories->count() > 0)
					@foreach($categories as $category)
						<div class="category-menu-item" style="position: relative; display: inline-block;">
							<a href="{{ route('client.products.index', ['category' => $category->id]) }}" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ request('category') == $category->id || (request('category') && in_array(request('category'), $category->children->pluck('id')->toArray())) ? 'how-active1' : '' }}" data-filter=".category-{{ $category->id }}">
								{{ $category->name }}
								@if($category->children && $category->children->count() > 0)
									<i class="zmdi zmdi-chevron-down" style="font-size: 14px; margin-left: 4px;"></i>
								@endif
							</a>
							
							@if($category->children && $category->children->count() > 0)
								<div class="category-submenu" style="position: absolute; top: 100%; left: 0; background: #fff; border: 1px solid #e6e6e6; border-radius: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); min-width: 200px; z-index: 100; display: none; margin-top: 0; padding: 5px 0;">
									@foreach($category->children as $child)
										<a href="{{ route('client.products.index', ['category' => $child->id]) }}" class="submenu-item stext-106 cl6 hov1 bor3 trans-04" style="display: block; padding: 8px 20px; margin: 0 10px; border-bottom: 1px solid #f0f0f0; {{ request('category') == $child->id ? 'how-active1' : '' }}">
											{{ $child->name }}
										</a>
									@endforeach
								</div>
							@endif
						</div>
					@endforeach
				@endif
			</div>
			
			<style>
				.category-menu-item {
					position: relative;
					display: inline-block;
				}
				.category-menu-item:hover .category-submenu {
					display: block !important;
				}
				.category-submenu:hover {
					display: block !important;
				}
				.submenu-item {
					text-decoration: none;
				}
				.submenu-item:hover,
				.submenu-item.how-active1 {
					color: #333 !important;
					border-color: #797979 !important;
				}
				.category-submenu .submenu-item:last-child {
					border-bottom: none !important;
				}
				.category-submenu .submenu-item {
					border-bottom-width: 1px;
					border-bottom-style: solid;
				}
			</style>

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

			@include('client.partials.filter-product')
		</div>

		<div class="row isotope-grid">
			@if(isset($products) && $products->count() > 0)
				@foreach($products as $product)
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item category-{{ $product->category_id }}">
					<!-- Block2 -->
					<div class="block2">
						<div class="block2-pic hov-img0">
							<img src="{{ $product->default_image_url }}" alt="{{ $product->name }}">

                            <a href="{{ route('client.products.index', array_filter(['quick_view' => $product->id, 'category' => request('category')])) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
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
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
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
					<p class="text-muted">Không tìm thấy sản phẩm nào</p>
				</div>
			@endif
		</div>
	</div>
</div>

@include('client.products.mini-product')
@endsection

