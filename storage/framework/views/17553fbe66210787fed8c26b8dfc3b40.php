	<!-- Footer -->
	<footer class="bg3 p-t-75 p-b-32">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Danh Mục
					</h4>

					<?php
						// Lấy danh mục hiển thị ở footer: ưu tiên $categories nếu controller đã truyền
						$footerCategories = null;
						if (isset($categories) && $categories instanceof \Illuminate\Support\Collection && $categories->count() > 0) {
							$footerCategories = $categories->take(4);
						} else {
							$footerCategories = \App\Models\Category::query()
								->where('status', 1)
								->orderBy('id')
								->take(4)
								->get();
						}
					?>
					<ul>
						<?php $__empty_1 = true; $__currentLoopData = $footerCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
							<li class="p-b-10">
								<a href="<?php echo e(route('client.products.index', ['category' => $category->id])); ?>" class="stext-107 cl7 hov-cl1 trans-04">
									<?php echo e($category->name); ?>

								</a>
							</li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
							<li class="p-b-10">
								<a href="<?php echo e(route('client.products.index')); ?>" class="stext-107 cl7 hov-cl1 trans-04">
									Nữ
								</a>
							</li>
							<li class="p-b-10">
								<a href="<?php echo e(route('client.products.index')); ?>" class="stext-107 cl7 hov-cl1 trans-04">
									Nam
								</a>
							</li>
							<li class="p-b-10">
								<a href="<?php echo e(route('client.products.index')); ?>" class="stext-107 cl7 hov-cl1 trans-04">
									Giày dép
								</a>
							</li>
							<li class="p-b-10">
								<a href="<?php echo e(route('client.products.index')); ?>" class="stext-107 cl7 hov-cl1 trans-04">
									Phụ kiện
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Hỗ trợ
					</h4>

					<ul>
						<li class="p-b-10">
							<a href="<?php echo e(route('client.order.track')); ?>" class="stext-107 cl7 hov-cl1 trans-04">
								Theo dõi đơn hàng
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Đổi trả hàng
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Vận chuyển
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Câu hỏi thường gặp
							</a>
						</li>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Liên hệ
					</h4>

					<p class="stext-107 cl7 size-201">
						Có câu hỏi? Hãy đến cửa hàng của chúng tôi hoặc liên hệ với chúng tôi qua số điện thoại để được hỗ trợ tốt nhất.
					</p>

					<div class="p-t-27">
						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-facebook"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-instagram"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-pinterest-p"></i>
						</a>
					</div>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Đăng ký nhận tin
					</h4>

					<form>
						<div class="wrap-input1 w-full p-b-4">
							<input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email" placeholder="email@example.com">
							<div class="focus-input1 trans-04"></div>
						</div>

						<div class="p-t-18">
							<button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
								Đăng ký
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="p-t-40">
				<div class="flex-c-m flex-w p-b-18">
					<a href="#" class="m-all-1">
						<img src="<?php echo e(asset('client/images/icons/icon-pay-01.png')); ?>" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="<?php echo e(asset('client/images/icons/icon-pay-02.png')); ?>" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="<?php echo e(asset('client/images/icons/icon-pay-03.png')); ?>" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="<?php echo e(asset('client/images/icons/icon-pay-04.png')); ?>" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="<?php echo e(asset('client/images/icons/icon-pay-05.png')); ?>" alt="ICON-PAY">
					</a>
				</div>

				<p class="stext-107 cl6 txt-center">
					Bản quyền &copy; 2025 STYLEX. Tất cả các quyền được bảo lưu.
			</div>
		</div>
	</footer>
	<!-- Back to top -->
	<div class="btn-back-to-top" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="zmdi zmdi-chevron-up"></i>
		</span>
	</div><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/partials/footer.blade.php ENDPATH**/ ?>