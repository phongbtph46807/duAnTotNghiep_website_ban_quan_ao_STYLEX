@extends('client.layout.layout')

@section('title', $product->name . ' - ' . env('APP_NAME'))

@push('styles')
<style>
    .product-image {
        width: 100%;
        height: auto;
        max-width: 100%;
        object-fit: contain;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        -ms-interpolation-mode: nearest-neighbor;
    }
    
    .wrap-pic-w img {
        width: 100% !important;
        height: auto !important;
        max-width: 100% !important;
        object-fit: contain !important;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        -ms-interpolation-mode: nearest-neighbor;
    }
</style>
@endpush

@section('content')

	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="{{ route('home') }}" class="stext-109 cl8 hov-cl1 trans-04">
				Trang Chủ
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<a href="{{ route('client.products.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
				Sản Phẩm
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				{{ $product->name }}
			</span>
		</div>
	</div>

	<!-- Product Detail -->
	<section class="sec-product-detail bg0 p-t-65 p-b-60">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-lg-7 p-b-30">
					<div class="p-l-25 p-r-30 p-lr-0-lg">
						<div class="wrap-slick3 flex-sb flex-w">
							<div class="wrap-slick3-dots"></div>
							<div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

							<div class="slick3 gallery-lb">
								@if($product->productImages && $product->productImages->count() > 0)
									@foreach($product->productImages as $image)
									<div class="item-slick3" data-thumb="{{ Storage::url($image->image_path) }}">
										<div class="wrap-pic-w pos-relative">
											<img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}" class="product-image">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="{{ Storage::url($image->image_path) }}">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
									@endforeach
								@else
									<div class="item-slick3" data-thumb="{{ $product->default_image_url }}">
										<div class="wrap-pic-w pos-relative">
											<img src="{{ $product->default_image_url }}" alt="{{ $product->name }}" class="product-image">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="{{ $product->default_image_url }}">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
					
				<div class="col-md-6 col-lg-5 p-b-30">
					<div class="p-r-50 p-t-5 p-lr-0-lg">
						<h4 class="mtext-105 cl2 js-name-detail p-b-14">
							{{ $product->name }}
						</h4>

						<span class="mtext-106 cl2">
							@if($product->price_sale && $product->price_sale < $product->price)
								<span class="fw-bold">{{ number_format($product->price_sale, 0, ',', '.') }}đ</span>
								<span style="text-decoration: line-through; color: red;">{{ number_format($product->price, 0, ',', '.') }}đ</span>
							@else
								{{ number_format($product->price, 0, ',', '.') }}đ
							@endif
						</span>

						<p class="stext-102 cl3 p-t-23">
							{{ $product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo.' }}
						</p>
						
						<!-- Product Options -->
						<div class="p-t-33">
							@if($product->productVariants && $product->productVariants->count() > 0)
								@php
									$sizes = $product->productVariants->pluck('size.name')->unique()->filter();
									$colors = $product->productVariants->pluck('color.name')->unique()->filter();
									$textures = $product->productVariants->pluck('texture.name')->unique()->filter();
								@endphp
								
								@if($sizes->count() > 0)
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Kích Thước
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="size" id="size-select">
												<option value="">Chọn kích thước</option>
												@foreach($sizes as $size)
												<option value="{{ $size }}">{{ $size }}</option>
												@endforeach
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								@endif

								@if($colors->count() > 0)
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Màu Sắc
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="color" id="color-select">
												<option value="">Chọn màu sắc</option>
												@foreach($colors as $color)
												<option value="{{ $color }}">{{ $color }}</option>
												@endforeach
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								@endif

								@if($textures->count() > 0)
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Chất Liệu
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="texture" id="texture-select">
												<option value="">Chọn chất liệu</option>
												@foreach($textures as $texture)
												<option value="{{ $texture }}">{{ $texture }}</option>
												@endforeach
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								@endif

								<!-- Hidden field để lưu variant_id -->
								<input type="hidden" id="selected-variant-id" name="variant_id" value="">
							@endif

							<div class="flex-w flex-r-m p-b-10" 
								 data-product-id="{{ $product->id }}"
								 data-variants="{{ json_encode($product->productVariants) }}"
								 data-original-price="{{ $product->price }}"
								 data-original-price-sale="{{ $product->price_sale }}">
								<div class="size-204 flex-w flex-m respon6-next">
									<div class="wrap-num-product flex-w m-r-20 m-tb-10">
										<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-minus"></i>
										</div>

										<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1" min="1">

										<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-plus"></i>
										</div>
									</div>

									<button class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail" data-product-id="{{ $product->id }}">
										Thêm vào giỏ
									</button>
								</div>
							</div>	
						</div>

						<!-- Social Share -->
						<div class="flex-w flex-m p-l-100 p-t-40 respon7">
							<div class="flex-m bor9 p-r-10 m-r-11">
								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100" data-tooltip="Thêm vào yêu thích">
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

			<div class="bor10 m-t-50 p-t-43 p-b-40">
				<!-- Tab01 -->
				<div class="tab01">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item p-b-10">
							<a class="nav-link active" data-toggle="tab" href="#description" role="tab">Mô tả</a>
						</li>

						<li class="nav-item p-b-10">
							<a class="nav-link" data-toggle="tab" href="#information" role="tab">Thông tin bổ sung</a>
						</li>

						<li class="nav-item p-b-10">
							<a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Đánh giá (0)</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content p-t-43">
						<!-- Description -->
						<div class="tab-pane fade show active" id="description" role="tabpanel">
							<div class="how-pos2 p-lr-15-md">
								<p class="stext-102 cl6">
									{{ $product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo. Được làm từ những nguyên liệu tốt nhất, đảm bảo độ bền và tính thẩm mỹ cao.' }}
								</p>
							</div>
						</div>

						<!-- Additional Information -->
						<div class="tab-pane fade" id="information" role="tabpanel">
							<div class="row">
								<div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
									<ul class="p-lr-28 p-lr-15-sm">
										<li class="flex-w flex-t p-b-7">
											<span class="stext-102 cl3 size-205">
												Danh mục
											</span>

											<span class="stext-102 cl6 size-206">
												{{ $product->category->name ?? 'N/A' }}
											</span>
										</li>

										<li class="flex-w flex-t p-b-7">
											<span class="stext-102 cl3 size-205">
												Trạng thái
											</span>

											<span class="stext-102 cl6 size-206">
												{{ $product->is_active ? 'Còn hàng' : 'Hết hàng' }}
											</span>
										</li>

										@if($product->productVariants && $product->productVariants->count() > 0)
											@php
												$sizes = $product->productVariants->pluck('size.name')->unique()->filter();
												$colors = $product->productVariants->pluck('color.name')->unique()->filter();
											@endphp
											
											@if($colors->count() > 0)
											<li class="flex-w flex-t p-b-7">
												<span class="stext-102 cl3 size-205">
													Màu sắc
												</span>

												<span class="stext-102 cl6 size-206">
													{{ $colors->implode(', ') }}
												</span>
											</li>
											@endif

											@if($sizes->count() > 0)
											<li class="flex-w flex-t p-b-7">
												<span class="stext-102 cl3 size-205">
													Kích thước
												</span>

												<span class="stext-102 cl6 size-206">
													{{ $sizes->implode(', ') }}
												</span>
											</li>
											@endif
										@endif
									</ul>
								</div>
							</div>
						</div>

						<!-- Reviews -->
						<div class="tab-pane fade" id="reviews" role="tabpanel">
							<div class="row">
								<div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
									<div class="p-b-30 m-lr-15-sm">
										<!-- Add review -->
										<form class="w-full">
											<h5 class="mtext-108 cl2 p-b-7">
												Thêm đánh giá
											</h5>

											<p class="stext-102 cl6">
												Email của bạn sẽ không được công khai. Các trường bắt buộc được đánh dấu *
											</p>

											<div class="flex-w flex-m p-t-50 p-b-23">
												<span class="stext-102 cl3 m-r-16">
													Đánh giá của bạn
												</span>

												<span class="wrap-rating fs-18 cl11 pointer">
													<i class="item-rating pointer zmdi zmdi-star-outline"></i>
													<i class="item-rating pointer zmdi zmdi-star-outline"></i>
													<i class="item-rating pointer zmdi zmdi-star-outline"></i>
													<i class="item-rating pointer zmdi zmdi-star-outline"></i>
													<i class="item-rating pointer zmdi zmdi-star-outline"></i>
													<input class="dis-none" type="number" name="rating">
												</span>
											</div>

											<div class="row p-b-25">
												<div class="col-12 p-b-5">
													<label class="stext-102 cl3" for="review">Đánh giá của bạn</label>
													<textarea class="size-110 bor8 stext-102 cl2 p-lr-20 p-tb-10" id="review" name="review"></textarea>
												</div>

												<div class="col-sm-6 p-b-5">
													<label class="stext-102 cl3" for="name">Tên</label>
													<input class="size-111 bor8 stext-102 cl2 p-lr-20" id="name" type="text" name="name">
												</div>

												<div class="col-sm-6 p-b-5">
													<label class="stext-102 cl3" for="email">Email</label>
													<input class="size-111 bor8 stext-102 cl2 p-lr-20" id="email" type="text" name="email">
												</div>
											</div>

											<button class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">
												Gửi đánh giá
											</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">
				<span class="stext-107 cl6 p-lr-25">
					SKU: {{ $product->id }}
				</span>

				<span class="stext-107 cl6 p-lr-25">
					Danh mục: {{ $product->category->name ?? 'N/A' }}
				</span>
			</div>
		</div>
	</section>

	<!-- Related Products -->
	@if($relatedProducts && $relatedProducts->count() > 0)
	<section class="sec-relate-product bg0 p-t-45 p-b-105">
		<div class="container">
			<div class="p-b-45">
				<h3 class="ltext-106 cl5 txt-center">
					Sản Phẩm Liên Quan
				</h3>
			</div>

			<!-- Slide2 -->
			<div class="wrap-slick2">
				<div class="slick2">
					@foreach($relatedProducts as $relatedProduct)
					<div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="{{ $relatedProduct->default_image_url }}" alt="{{ $relatedProduct->name }}">

								<a href="{{ route('client.products.show', $relatedProduct->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
									Xem Chi Tiết
								</a>
							</div>

							<div class="block2-txt flex-w flex-t p-t-14">
								<div class="block2-txt-child1 flex-col-l ">
									<a href="{{ route('client.products.show', $relatedProduct->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
										{{ $relatedProduct->name }}
									</a>

									<span class="stext-105 cl3">
										@if($relatedProduct->price_sale && $relatedProduct->price_sale < $relatedProduct->price)
											<span class="fw-bold">{{ number_format($relatedProduct->price_sale, 0, ',', '.') }}đ</span>
											<span style="text-decoration: line-through; color: red;">{{ number_format($relatedProduct->price, 0, ',', '.') }}đ</span>
										@else
											{{ number_format($relatedProduct->price, 0, ',', '.') }}đ
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
				</div>
			</div>
		</div>
	</section>
	@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Lấy dữ liệu từ data attributes
    const productContainer = $('[data-product-id]');
    const variants = JSON.parse(productContainer.data('variants') || '[]');
    const originalPrice = parseFloat(productContainer.data('original-price') || 0);
    const originalPriceSale = parseFloat(productContainer.data('original-price-sale') || 0);
    const hasPriceSale = originalPriceSale && originalPriceSale < originalPrice;
    
    // Function để tìm variant dựa trên size, color, texture
    function findVariant(size, color, texture) {
        return variants.find(variant => {
            const variantSize = variant.size ? variant.size.name : null;
            const variantColor = variant.color ? variant.color.name : null;
            const variantTexture = variant.texture ? variant.texture.name : null;
            
            return variantSize === size && 
                   variantColor === color && 
                   variantTexture === texture;
        });
    }
    
    // Function để cập nhật giá khi chọn variant
    function updatePrice() {
        const size = $('#size-select').val();
        const color = $('#color-select').val();
        const texture = $('#texture-select').val();
        
        const variant = findVariant(size, color, texture);
        
        if (variant && variant.price > 0) {
            // Cập nhật giá hiển thị
            $('.mtext-106').html('<span class="fw-bold">' + 
                new Intl.NumberFormat('vi-VN').format(variant.price) + 'đ</span>');
            
            // Lưu variant_id
            $('#selected-variant-id').val(variant.id);
        } else {
            // Reset về giá gốc của product
            if (hasPriceSale) {
                $('.mtext-106').html('<span class="fw-bold">' + 
                    new Intl.NumberFormat('vi-VN').format(originalPriceSale) + 'đ</span>' +
                    '<span style="text-decoration: line-through; color: red;">' + 
                    new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ</span>');
            } else {
                $('.mtext-106').html(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
            }
            
            $('#selected-variant-id').val('');
        }
    }
    
    // Event listeners cho các dropdown
    $('#size-select, #color-select, #texture-select').on('change', function() {
        updatePrice();
    });
    
    // Quantity controls
    $('.btn-num-product-down').on('click', function() {
        const input = $(this).siblings('.num-product');
        const currentValue = parseInt(input.val());
        if (currentValue > 1) {
            input.val(currentValue - 1);
        }
    });
    
    $('.btn-num-product-up').on('click', function() {
        const input = $(this).siblings('.num-product');
        const currentValue = parseInt(input.val());
        input.val(currentValue + 1);
    });
    
    // Add to cart
    $('.js-addcart-detail').on('click', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        const quantity = $('.num-product').val();
        const variantId = $('#selected-variant-id').val();
        
        // Kiểm tra nếu có variants nhưng chưa chọn
        if (variants.length > 0 && !variantId) {
            alert('Vui lòng chọn đầy đủ thông tin sản phẩm (kích thước, màu sắc, chất liệu)');
            return;
        }
        
        // Gửi AJAX request
        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                variant_id: variantId || null,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    alert(response.message);
                    
                    // Có thể cập nhật số lượng trong giỏ hàng ở header
                    if (response.cart_count) {
                        $('.cart-count').text(response.cart_count);
                    }
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    alert('Có lỗi xảy ra: ' + response.message);
                } else {
                    alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                }
            }
        });
    });
});
</script>
@endpush
