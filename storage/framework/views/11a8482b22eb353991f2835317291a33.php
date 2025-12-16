		<div class="container-menu-desktop">
			<!-- Topbar -->
			<div class="top-bar">
				<div class="content-topbar flex-sb-m h-full container">
					<div class="left-top-bar">
					Khi mua đơn hàng 200k sẽ được free ship 
					</div>

					<div class="right-top-bar flex-w h-full">
						<a href="#" class="flex-c-m trans-04 p-lr-25">
							Trợ giúp & FAQs
						</a>

						<?php if(auth()->guard()->check()): ?>
							<a href="<?php echo e(route('client.profile.index')); ?>" class="flex-c-m trans-04 p-lr-25">
								Tài Khoản
							</a>
						<?php else: ?>
							<a href="<?php echo e(route('loginView')); ?>" class="flex-c-m trans-04 p-lr-25">
							Tài Khoản
						</a>
						<?php endif; ?>

					</div>
				</div>
			</div>

			<div class="wrap-menu-desktop how-shadow1">
				<nav class="limiter-menu-desktop container">
						
					<!-- Logo desktop -->		
					<a href="<?php echo e(route('home')); ?>" class="logo">
					STYLE<span>X</span>
					</a>

					<!-- Menu desktop -->
                   					<div class="menu-desktop">
					    <ul class="main-menu">
					        <li class="<?php echo e(request()->routeIs('home') ? 'active-menu' : ''); ?>">
					            <a href="<?php echo e(route('home')); ?>">Trang Chủ</a>
					        </li>

					        <li class="<?php echo e(request()->routeIs('client.products.*') ? 'active-menu' : ''); ?>">
					            <a href="<?php echo e(route('client.products.index')); ?>">Sản Phẩm</a>
					        </li>

					        <li class="label1 <?php echo e(request()->is('shoping-cart') ? 'active-menu' : ''); ?>" data-label1="hot">
					            <a href="shoping-cart.html">Sắp Ra Mắt</a>
					        </li>

					        <li class="<?php echo e(request()->is('blog') ? 'active-menu' : ''); ?>">
					            <a href="<?php echo e(route('blog.index')); ?>">Blog</a>
					        </li>

					        <li class="<?php echo e(request()->is('contact') ? 'active-menu' : ''); ?>">
					            <a href="contact.html">Liên Hệ</a>
					        </li>
					    </ul>
					</div>
                    
					<!-- Icon header -->
					<?php
						if (\Illuminate\Support\Facades\Auth::check()) {
							$cartCount = \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())->sum('quantity');
							// include session items if any left from guest
							$sessionItems = session('cart.items', []);
							foreach ($sessionItems as $it) { $cartCount += (int)($it['quantity'] ?? 0); }
						} else {
							$sessionItems = session('cart.items', []);
							$cartCount = 0;
							foreach ($sessionItems as $it) { $cartCount += (int)($it['quantity'] ?? 0); }
						}
					?>
					<div class="wrap-icon-header flex-w flex-r-m">
						<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
							<i class="zmdi zmdi-search"></i>
						</div>

						<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="<?php echo e($cartCount); ?>">
							<i class="zmdi zmdi-shopping-cart"></i>
						</div>

						<a href="#" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
							<i class="zmdi zmdi-favorite-outline"></i>
						</a>
						
						<?php if(auth()->guard()->check()): ?>
							<?php 
								$authUser = Auth::user();
								$loyaltyService = app(\App\Services\LoyaltyService::class);
								$currentTier = null;
								$nextTierProgress = null;
								$totalSpent = 0;
								if ($authUser instanceof \App\Models\User) {
								$currentTier = $loyaltyService->getCurrentTier($authUser);
								$nextTierProgress = $loyaltyService->getNextTierProgress($authUser);
								$totalSpent = $authUser->getTotalSpent();
								}
								
								// Tính toán màu sắc cho badge từ database
								$tierBgColor = '#8B4513'; // Màu mặc định: nâu đồng (Đồng)
								$tierTextColor = '#fff';
								if ($currentTier) {
									// Refresh tier từ database để đảm bảo có đầy đủ thông tin
									$currentTier->refresh();
									$tierBgColor = $currentTier->color ?? '#8B4513';
									$tierTextColor = $currentTier->text_color ?? '#fff';
								}
								
								// Tính toán progress width
								$progressWidth = 0;
								if ($nextTierProgress) {
									$progressWidth = min(100, $nextTierProgress['progress']);
								}
								
								// Tính toán các style strings
								$badgeStyle = "background: {$tierBgColor}; color: {$tierTextColor}; font-size: 9px; padding: 2px 5px; border-radius: 3px; line-height: 1.2; flex-shrink: 0;";
								$badgeStyleLarge = "background: {$tierBgColor}; color: {$tierTextColor}; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 600;";
								$progressStyle = "background: #6777ef; height: 100%; width: {$progressWidth}%; transition: width 0.3s;";
							?>
							<!-- User đã đăng nhập -->
							<div class="dropdown">
								<a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 dropdown-toggle" data-bs-toggle="dropdown" style="display: flex; align-items: center; gap: 6px;">
									<i class="zmdi zmdi-account"></i>
									<span class="ml-2" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($authUser->name ?? 'Tài khoản'); ?></span>
									<?php if($currentTier): ?>
										<span class="badge" style="<?php echo e($badgeStyle); ?>">
											<?php echo e($currentTier->name); ?>

										</span>
									<?php endif; ?>
								</a>
								<div class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
									<div class="dropdown-header" style="padding: 12px 16px;">
										<div class="user-info">
											<?php if($currentTier): ?>
												<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee;">
													<div style="display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
														<span style="font-size: 11px; color: #666; font-weight: 500;">Hạng:</span>
														<span class="badge" style="<?php echo e($badgeStyleLarge); ?>">
															<?php echo e($currentTier->name); ?>

														</span>
														<?php if($currentTier->discount_rate > 0): ?>
															<span style="font-size: 11px; color: #28a745; font-weight: 600;">-<?php echo e(number_format($currentTier->discount_rate, 0)); ?>%</span>
														<?php endif; ?>
														<span style="font-size: 11px; color: #888;">•</span>
														<span style="font-size: 11px; color: #888;">
															Đã chi: <strong style="color: #333;"><?php echo e(number_format($totalSpent, 0, ',', '.')); ?> ₫</strong>
														</span>
													</div>
													<?php if($nextTierProgress): ?>
														<div style="margin-top: 8px;">
															<div style="font-size: 11px; color: #666; margin-bottom: 4px;">
																Lên <strong><?php echo e($nextTierProgress['next_tier']->name); ?></strong> còn: <strong style="color: #6777ef;"><?php echo e(number_format($nextTierProgress['remaining'], 0, ',', '.')); ?> ₫</strong>
															</div>
															<div style="background: #f0f0f0; border-radius: 4px; height: 6px; overflow: hidden;">
																<div style="<?php echo e($progressStyle); ?>"></div>
															</div>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="<?php echo e(route('client.profile.index')); ?>">
										<i class="zmdi zmdi-account-circle me-2"></i>
										Hồ sơ cá nhân
									</a>
									<a class="dropdown-item" href="<?php echo e(route('client.order.list')); ?>">
										<i class="zmdi zmdi-shopping-cart me-2"></i>
										Đơn hàng của tôi
									</a>
									<a class="dropdown-item" href="<?php echo e(route('client.order.track')); ?>">
										<i class="zmdi zmdi-search me-2"></i>
										Tra cứu đơn hàng
									</a>
									<a class="dropdown-item" href="#">
										<i class="zmdi zmdi-favorite me-2"></i>
										Yêu thích
									</a>
									<a class="dropdown-item" href="#">
										<i class="zmdi zmdi-settings me-2"></i>
										Cài đặt
									</a>
									<div class="dropdown-divider"></div>
									<form method="POST" action="<?php echo e(route('logout')); ?>">
										<?php echo csrf_field(); ?>
										<button type="submit" class="dropdown-item logout-btn">
											<i class="zmdi zmdi-power me-2"></i>
											Đăng xuất
										</button>
									</form>
								</div>
							</div>
						<?php else: ?>
							<!-- User chưa đăng nhập -->
							<a href="<?php echo e(route('loginView')); ?>" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
								<i class="zmdi zmdi-account"></i>
							</a>
						<?php endif; ?>
					</div>
				</nav>
			</div>	
		</div>

		 <!-- Modal Search -->
		<div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
			<div class="container-search-header">
				<button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
					<img src="<?php echo e(asset('client/images/icons/icon-close2.png')); ?>" alt="CLOSE">
				</button>

				<form class="wrap-search-header flex-w p-l-15">
					<button class="flex-c-m trans-04">
						<i class="zmdi zmdi-search"></i>
					</button>
					<input class="plh3" type="text" name="search" placeholder="Search...">
				</form>
			</div>
		</div><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/duAnTotNghiep_website_ban_quan_ao_STYLEX/resources/views/client/partials/sidebar.blade.php ENDPATH**/ ?>