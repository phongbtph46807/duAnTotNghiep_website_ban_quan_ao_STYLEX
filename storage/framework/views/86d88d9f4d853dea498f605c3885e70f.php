		<!-- Header Mobile -->
		<div class="wrap-header-mobile">
			<!-- Logo moblie -->		
			<div class="logo-mobile">
				<!-- Logo desktop -->		
					<a href="<?php echo e(route('home')); ?>" class="logo">
					STYLE<span>X</span>
					</a>
			</div>

			<!-- Icon header -->
			<div class="wrap-icon-header flex-w flex-r-m m-r-15">
		<div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
			<i class="zmdi zmdi-search"></i>
		</div>

		<?php
			if (\Illuminate\Support\Facades\Auth::check()) {
				$cartCountMobile = \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())->sum('quantity');
				$sessionItems = session('cart.items', []);
				foreach ($sessionItems as $it) { $cartCountMobile += (int)($it['quantity'] ?? 0); }
			} else {
				$sessionItems = session('cart.items', []);
				$cartCountMobile = 0;
				foreach ($sessionItems as $it) { $cartCountMobile += (int)($it['quantity'] ?? 0); }
			}
		?>
		<div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" data-notify="<?php echo e($cartCountMobile); ?>">
			<i class="zmdi zmdi-shopping-cart"></i>
		</div>
			
		<a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti" data-notify="0">
			<i class="zmdi zmdi-favorite-outline"></i>
		</a>
											
		<?php if(auth()->guard()->check()): ?>
			<?php $authUser = Auth::user(); ?>
			<a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-account-menu">
				<i class="zmdi zmdi-account"></i>
			</a>
		<?php else: ?>
			<a href="<?php echo e(route('loginView')); ?>" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
						<i class="zmdi zmdi-account"></i>
					</a>
		<?php endif; ?>
			
			</div>

			<!-- Button show menu -->
			<div class="btn-show-menu-mobile hamburger hamburger--squeeze">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</div>
		</div>


		<!-- Menu Mobile -->
		<div class="menu-mobile">
			<ul class="topbar-mobile">
				<li>
					<div class="left-top-bar">
						Free shipping for standard order over $100
					</div>
				</li>

				<li>
					<div class="right-top-bar flex-w h-full">
						<a href="#" class="flex-c-m p-lr-10 trans-04">
							Help & FAQs
						</a>

						<a href="#" class="flex-c-m p-lr-10 trans-04">
							My Account
						</a>

						<a href="#" class="flex-c-m p-lr-10 trans-04">
							EN
						</a>

						<a href="#" class="flex-c-m p-lr-10 trans-04">
							USD
						</a>
					</div>
				</li>
			</ul>

			<ul class="main-menu-m">
				<li>
					<a href="<?php echo e(route('home')); ?>">Trang Chủ</a>
				</li>

				<li>
					<a href="<?php echo e(route('client.products.index')); ?>">Sản Phẩm</a>
				</li>

				<li>
					<a href="<?php echo e(route('blog.index')); ?>">Blog</a>
				</li>

				<li>
					<a href="contact.html">Liên Hệ</a>
				</li>

				<?php if(auth()->guard()->check()): ?>
					<li>
						<a href="#" class="js-toggle-account-menu">Tài Khoản</a>
						<ul class="sub-menu-m account-menu-mobile">
				<li>
								<div style="padding: 10px 15px; border-bottom: 1px solid #eee;">
									<div style="font-weight: 600;"><?php echo e(Auth::user()->name ?? ''); ?></div>
									<div style="font-size: 12px; color: #666;"><?php echo e(Auth::user()->email ?? ''); ?></div>
								</div>
				</li>
							<li><a href="<?php echo e(route('client.order.list')); ?>"><i class="zmdi zmdi-shopping-cart" style="margin-right: 8px;"></i>Đơn hàng của tôi</a></li>
							<li><a href="<?php echo e(route('client.order.track')); ?>"><i class="zmdi zmdi-search" style="margin-right: 8px;"></i>Tra cứu đơn hàng</a></li>
							<li><a href="#"><i class="zmdi zmdi-favorite" style="margin-right: 8px;"></i>Yêu thích</a></li>
							<li>
								<form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
									<?php echo csrf_field(); ?>
									<button type="submit" style="background: none; border: none; color: inherit; width: 100%; text-align: left; padding: 10px 15px; cursor: pointer;">
										<i class="zmdi zmdi-power" style="margin-right: 8px;"></i>Đăng xuất
									</button>
								</form>
							</li>
						</ul>
						<span class="arrow-main-menu-m">
							<i class="fa fa-angle-right" aria-hidden="true"></i>
						</span>
				</li>
				<?php endif; ?>
			</ul>
		</div>
<<<<<<<< HEAD:storage/framework/views/86d88d9f4d853dea498f605c3885e70f.php
<?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/partials/mobile.blade.php ENDPATH**/ ?>
========
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/duAnTotNghiep_website_ban_quan_ao_STYLEX/resources/views/client/partials/mobile.blade.php ENDPATH**/ ?>
>>>>>>>> origin:storage/framework/views/84c9f52816a17839fca83e05b327ebd9.php
