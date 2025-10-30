<?php $__env->startSection('title', $product->name . ' - ' . env('APP_NAME')); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="<?php echo e(route('home')); ?>" class="stext-109 cl8 hov-cl1 trans-04">
				Trang Chủ
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<a href="<?php echo e(route('client.products.index')); ?>" class="stext-109 cl8 hov-cl1 trans-04">
				Sản Phẩm
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				<?php echo e($product->name); ?>

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
								<?php if($product->productImages && $product->productImages->count() > 0): ?>
									<?php $__currentLoopData = $product->productImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="item-slick3" data-thumb="<?php echo e(Storage::url($image->image_path)); ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo e(Storage::url($image->image_path)); ?>" alt="<?php echo e($product->name); ?>" class="product-image">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo e(Storage::url($image->image_path)); ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php else: ?>
									<div class="item-slick3" data-thumb="<?php echo e($product->default_image_url); ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo e($product->default_image_url); ?>" alt="<?php echo e($product->name); ?>" class="product-image">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo e($product->default_image_url); ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
					
				<div class="col-md-6 col-lg-5 p-b-30">
					<div class="p-r-50 p-t-5 p-lr-0-lg">
						<h4 class="mtext-105 cl2 js-name-detail p-b-14">
							<?php echo e($product->name); ?>

						</h4>

						<span class="mtext-106 cl2">
							<?php if($product->price_sale && $product->price_sale < $product->price): ?>
								<span class="fw-bold"><?php echo e(number_format($product->price_sale, 0, ',', '.')); ?>đ</span>
								<span style="text-decoration: line-through; color: red;"><?php echo e(number_format($product->price, 0, ',', '.')); ?>đ</span>
							<?php else: ?>
								<?php echo e(number_format($product->price, 0, ',', '.')); ?>đ
							<?php endif; ?>
						</span>

						<p class="stext-102 cl3 p-t-23">
							<?php echo e($product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo.'); ?>

						</p>
						
						<!-- Product Options -->
						<div class="p-t-33">
							<?php if($product->productVariants && $product->productVariants->count() > 0): ?>
								<?php
									$sizes = $product->productVariants->pluck('size.name')->unique()->filter();
									$colors = $product->productVariants->pluck('color.name')->unique()->filter();
									$textures = $product->productVariants->pluck('texture.name')->unique()->filter();
								?>
								
								<?php if($sizes->count() > 0): ?>
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Kích Thước
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="size" id="size-select">
												<option value="">Chọn kích thước</option>
												<?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($size); ?>"><?php echo e($size); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								<?php endif; ?>

								<?php if($colors->count() > 0): ?>
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Màu Sắc
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="color" id="color-select">
												<option value="">Chọn màu sắc</option>
												<?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($color); ?>"><?php echo e($color); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								<?php endif; ?>

								<?php if($textures->count() > 0): ?>
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Chất Liệu
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="texture" id="texture-select">
												<option value="">Chọn chất liệu</option>
												<?php $__currentLoopData = $textures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $texture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($texture); ?>"><?php echo e($texture); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>
								<?php endif; ?>

							<?php endif; ?>

							<div class="flex-w flex-r-m p-b-10" 
								 data-product-id="<?php echo e($product->id); ?>"
								 data-variants="<?php echo e(json_encode($product->productVariants)); ?>"
								 data-original-price="<?php echo e($product->price); ?>"
								 data-original-price-sale="<?php echo e($product->price_sale); ?>">
                              <form id="add-to-cart-form" method="POST" action="<?php echo e(route('client.cart.add')); ?>" data-ajax="1" class="size-204 flex-w flex-m respon6-next">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                                    <input type="hidden" name="variant_id" value="">
                                    <input type="hidden" name="size_name" value="">
                                    <input type="hidden" name="color_name" value="">
                                    <input type="hidden" name="texture_name" value="">
                                    <div class="wrap-num-product flex-w m-r-20 m-tb-10">
										<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-minus"></i>
										</div>

                                        <input class="mtext-104 cl3 txt-center num-product" type="number" name="quantity" value="1" min="1">

										<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-plus"></i>
										</div>
									</div>

                                    <button type="submit" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
										Thêm vào giỏ
									</button>
                                </form>
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
									<?php echo e($product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo. Được làm từ những nguyên liệu tốt nhất, đảm bảo độ bền và tính thẩm mỹ cao.'); ?>

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
												<?php echo e($product->category->name ?? 'N/A'); ?>

											</span>
										</li>

										<li class="flex-w flex-t p-b-7">
											<span class="stext-102 cl3 size-205">
												Trạng thái
											</span>

											<span class="stext-102 cl6 size-206">
												<?php echo e($product->is_active ? 'Còn hàng' : 'Hết hàng'); ?>

											</span>
										</li>

										<?php if($product->productVariants && $product->productVariants->count() > 0): ?>
											<?php
												$sizes = $product->productVariants->pluck('size.name')->unique()->filter();
												$colors = $product->productVariants->pluck('color.name')->unique()->filter();
											?>
											
											<?php if($colors->count() > 0): ?>
											<li class="flex-w flex-t p-b-7">
												<span class="stext-102 cl3 size-205">
													Màu sắc
												</span>

												<span class="stext-102 cl6 size-206">
													<?php echo e($colors->implode(', ')); ?>

												</span>
											</li>
											<?php endif; ?>

											<?php if($sizes->count() > 0): ?>
											<li class="flex-w flex-t p-b-7">
												<span class="stext-102 cl3 size-205">
													Kích thước
												</span>

												<span class="stext-102 cl6 size-206">
													<?php echo e($sizes->implode(', ')); ?>

												</span>
											</li>
											<?php endif; ?>
										<?php endif; ?>
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
					SKU: <?php echo e($product->id); ?>

				</span>

				<span class="stext-107 cl6 p-lr-25">
					Danh mục: <?php echo e($product->category->name ?? 'N/A'); ?>

				</span>
			</div>
		</div>
	</section>

	<!-- Related Products -->
	<?php if($relatedProducts && $relatedProducts->count() > 0): ?>
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
					<?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="<?php echo e($relatedProduct->default_image_url); ?>" alt="<?php echo e($relatedProduct->name); ?>">

								<a href="<?php echo e(route('client.products.show', $relatedProduct->id)); ?>" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
									Xem Chi Tiết
								</a>
							</div>

							<div class="block2-txt flex-w flex-t p-t-14">
								<div class="block2-txt-child1 flex-col-l ">
									<a href="<?php echo e(route('client.products.show', $relatedProduct->id)); ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
										<?php echo e($relatedProduct->name); ?>

									</a>

									<span class="stext-105 cl3">
										<?php if($relatedProduct->price_sale && $relatedProduct->price_sale < $relatedProduct->price): ?>
											<span class="fw-bold"><?php echo e(number_format($relatedProduct->price_sale, 0, ',', '.')); ?>đ</span>
											<span style="text-decoration: line-through; color: red;"><?php echo e(number_format($relatedProduct->price, 0, ',', '.')); ?>đ</span>
										<?php else: ?>
											<?php echo e(number_format($relatedProduct->price, 0, ',', '.')); ?>đ
										<?php endif; ?>
									</span>
								</div>

								<div class="block2-txt-child2 flex-r p-t-3">
									<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
										<img class="icon-heart1 dis-block trans-04" src="<?php echo e(asset('client/images/icons/icon-heart-01.png')); ?>" alt="ICON">
										<img class="icon-heart2 dis-block trans-04 ab-t-l" src="<?php echo e(asset('client/images/icons/icon-heart-02.png')); ?>" alt="ICON">
									</a>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Lấy dữ liệu từ data attributes
    const productContainer = $('[data-product-id]');
    const variants = JSON.parse(productContainer.data('variants') || '[]');
    const originalPrice = parseFloat(productContainer.data('original-price') || 0);
    const originalPriceSale = parseFloat(productContainer.data('original-price-sale') || 0);
    const hasPriceSale = originalPriceSale && originalPriceSale < originalPrice;
    
    // Function để tìm variant dựa trên size, color, texture (chỉ so khớp các trường đã chọn)
    function findVariant(size, color, texture) {
        return variants.find(variant => {
            const vSize = variant.size ? variant.size.name : null;
            const vColor = variant.color ? variant.color.name : null;
            const vTexture = variant.texture ? variant.texture.name : null;

            const okSize = !size || size === vSize;
            const okColor = !color || color === vColor;
            const okTexture = !texture || texture === vTexture;
            return okSize && okColor && okTexture;
        });
    }
    
    // Function để cập nhật giá khi chọn variant
    function updatePrice() {
        const size = $('#size-select').val();
        const color = $('#color-select').val();
        const texture = $('#texture-select').val();
        
        const variant = findVariant(size, color, texture);
        
        if (variant) {
            // Luôn lưu variant_id kể cả khi giá = 0 (fallback về giá product)
            $('input[name="variant_id"]').val(variant.id);

            if (variant.price && parseFloat(variant.price) > 0) {
                $('.mtext-106').html('<span class="fw-bold">' + 
                    new Intl.NumberFormat('vi-VN').format(variant.price) + 'đ</span>');
            } else {
                if (hasPriceSale) {
                    $('.mtext-106').html('<span class="fw-bold">' + 
                        new Intl.NumberFormat('vi-VN').format(originalPriceSale) + 'đ</span>' +
                        '<span style="text-decoration: line-through; color: red;">' + 
                        new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ</span>');
                } else {
                    $('.mtext-106').html(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
                }
            }
        } else {
            // Không khớp biến thể nào -> reset giá và xoá variant_id
            if (hasPriceSale) {
                $('.mtext-106').html('<span class="fw-bold">' + 
                    new Intl.NumberFormat('vi-VN').format(originalPriceSale) + 'đ</span>' +
                    '<span style="text-decoration: line-through; color: red;">' + 
                    new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ</span>');
            } else {
                $('.mtext-106').html(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
            }
            $('input[name="variant_id"]').val('');
        }
    }
    
    // Đồng bộ hidden fields và variant khi mở trang (trường hợp đã có sẵn lựa chọn)
    (function syncInitial() {
        $('input[name="size_name"]').val($('#size-select').val() || '');
        $('input[name="color_name"]').val($('#color-select').val() || '');
        $('input[name="texture_name"]').val($('#texture-select').val() || '');
        updatePrice();
    })();

    // Event listeners cho các dropdown
    $('#size-select, #color-select, #texture-select').on('change', function() {
        // update hidden attribute names
        $('input[name="size_name"]').val($('#size-select').val() || '');
        $('input[name="color_name"]').val($('#color-select').val() || '');
        $('input[name="texture_name"]').val($('#texture-select').val() || '');
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
    
    // Chặn submit nếu có biến thể mà chưa chọn -> tránh lưu null
    $('#add-to-cart-form').off('submit').on('submit', function(e){
        const variantId = $('input[name="variant_id"]').val();
        if (variants && variants.length > 0 && !variantId) {
            e.preventDefault();
            alert('Vui lòng chọn đầy đủ thông tin sản phẩm (kích thước, màu sắc, chất liệu)');
            return false;
        }
        return true;
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/product/detail.blade.php ENDPATH**/ ?>