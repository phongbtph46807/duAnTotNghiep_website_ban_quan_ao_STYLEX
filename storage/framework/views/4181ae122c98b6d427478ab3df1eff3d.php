	<div class="wrap-header-cart js-panel-cart">
		<div class="s-full js-hide-cart"></div>

		<div class="header-cart flex-col-l p-l-35 p-r-35 p-t-25">
			<div class="header-cart-title flex-w flex-sb-m p-b-15">
				<h4 class="mtext-103 cl2" style="font-size: 20px; font-weight: bold;">
					Giỏ Hàng
				</h4>

				<button class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart" style="background: none; border: none; cursor: pointer;">
					<i class="zmdi zmdi-close"></i>
				</button>
			</div>
			
			<div class="header-cart-content flex-w js-pscroll" id="cartContent" style="flex: 1; overflow-y: auto;">
				<ul class="header-cart-wrapitem w-full" id="cartItems">
					<li class="header-cart-empty" style="padding: 60px 20px; text-align: center; color: #999;">
						<i class="zmdi zmdi-shopping-cart" style="font-size: 64px; opacity: 0.3;"></i>
						<p style="margin-top: 20px; font-size: 16px;">Giỏ hàng trống</p>
					</li>
				</ul>
				
				<div class="w-full" id="cartFooter" style="display: none;">
					<div class="header-cart-total w-full p-tb-30" id="cartTotal" style="border-top: 1px solid #e8e8e8;">
						<div class="flex-w flex-sb-m">
							<span class="mtext-107 cl2" style="font-size: 18px; font-weight: 600;">
								Tổng cộng:
							</span>
							<span class="mtext-106 cl2" id="totalAmount" style="font-size: 20px; font-weight: 700; color: #666;">
								0 VNĐ
							</span>
						</div>
					</div>

					<div class="header-cart-buttons flex-w w-full" style="gap: 10px;">
						<a href="<?php echo e(route('client.cart.index')); ?>" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10" style="flex: 1;">
							Xem Giỏ Hàng
						</a>

						<a href="#" class="flex-c-m stext-101 cl0 size-107 bg1 bor2 hov-btn3 p-lr-15 trans-04 m-b-10" style="flex: 1; background: #333;">
							Thanh Toán
						</a>
					</div>
				</div>
			</div>
		</div>
	</div><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/partials/cart.blade.php ENDPATH**/ ?>