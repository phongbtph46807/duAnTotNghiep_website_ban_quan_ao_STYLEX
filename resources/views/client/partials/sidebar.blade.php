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

						@auth
							<a href="{{ route('client.profile.index') }}" class="flex-c-m trans-04 p-lr-25">
								Tài Khoản
							</a>
						@else
							<a href="{{ route('loginView') }}" class="flex-c-m trans-04 p-lr-25">
							Tài Khoản
						</a>
						@endauth

					</div>
				</div>
			</div>

			<div class="wrap-menu-desktop how-shadow1">
				<nav class="limiter-menu-desktop container">
						
					<!-- Logo desktop -->		
					<a href="{{ route('home') }}" class="logo">
					STYLE<span>X</span>
					</a>

					<!-- Menu desktop -->
                   					<div class="menu-desktop">
					    <ul class="main-menu">
					        <li class="{{ request()->routeIs('home') ? 'active-menu' : '' }}">
					            <a href="{{ route('home') }}">Trang Chủ</a>
					        </li>

					        <li class="{{ request()->routeIs('client.products.*') ? 'active-menu' : '' }}">
					            <a href="{{ route('client.products.index') }}">Sản Phẩm</a>
					        </li>

					        <li class="label1 {{ request()->is('shoping-cart') ? 'active-menu' : '' }}" data-label1="hot">
					            <a href="shoping-cart.html">Sắp Ra Mắt</a>
					        </li>

					        <li class="{{ request()->is('blog') ? 'active-menu' : '' }}">
					            <a href="{{route('blog.index')}}">Blog</a>
					        </li>

					        <li class="{{ request()->is('contact') ? 'active-menu' : '' }}">
					            <a href="{{ route('contact.index') }}">Liên Hệ</a>
					        </li>
					    </ul>
					</div>
                    
					<!-- Icon header -->
					@php
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
					@endphp
					<div class="wrap-icon-header flex-w flex-r-m">
						<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
							<i class="zmdi zmdi-search"></i>
						</div>

						<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="{{ $cartCount }}">
							<i class="zmdi zmdi-shopping-cart"></i>
						</div>

						@auth
							<a href="{{ route('client.wishlist.index') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="{{ Auth::user()->wishlistProducts()->count() }}">
								<i class="zmdi zmdi-favorite-outline"></i>
							</a>
							
							@php
								// Chỉ đếm thông báo về đơn hàng của user
								$unreadCount = DB::table('notifications')
									->where('user_id', Auth::id())
									->where('type', 'order_status_changed')
									->whereNull('read_at')
									->count();
							@endphp
							<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-notifications" data-notify="{{ $unreadCount }}" style="position: relative; cursor: pointer;">
								<i class="zmdi zmdi-notifications"></i>
								<!-- Notification Dropdown -->
								<div class="notification-dropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 350px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-radius: 8px; z-index: 10000; margin-top: 10px; max-height: 500px; overflow-y: auto;">
									<div class="notification-header" style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
										<h6 style="margin: 0; font-weight: 600; font-size: 14px;">Thông báo</h6>
										<button class="mark-all-read-btn" style="background: none; border: none; color: #6777ef; cursor: pointer; font-size: 12px; padding: 0;">Đánh dấu tất cả đã đọc</button>
									</div>
									<div class="notification-list" style="padding: 0;">
										<div class="notification-loading" style="padding: 20px; text-align: center; color: #999;">
											<i class="zmdi zmdi-spinner zmdi-hc-spin" style="font-size: 24px;"></i>
										</div>
									</div>
									<div class="notification-footer" style="padding: 10px; text-align: center; border-top: 1px solid #eee;">
										<a href="{{ route('client.notifications.index') }}" style="color: #6777ef; text-decoration: none; font-size: 12px;">Xem tất cả</a>
									</div>
								</div>
							</div>
						@else
							<a href="{{ route('loginView') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
								<i class="zmdi zmdi-favorite-outline"></i>
							</a>
						@endauth
						
						@auth
							@php 
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
							@endphp
							<!-- User đã đăng nhập -->
							<div class="dropdown">
								<a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 dropdown-toggle" data-bs-toggle="dropdown" style="display: flex; align-items: center; gap: 6px;">
									<i class="zmdi zmdi-account"></i>
									<span class="ml-2" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $authUser->name ?? 'Tài khoản' }}</span>
									@if($currentTier)
										<span class="badge" style="{{ $badgeStyle }}">
											{{ $currentTier->name }}
										</span>
									@endif
								</a>
								<div class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
									<div class="dropdown-header" style="padding: 12px 16px;">
										<div class="user-info">
											@if($currentTier)
												<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee;">
													<div style="display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
														<span style="font-size: 11px; color: #666; font-weight: 500;">Hạng:</span>
														<span class="badge" style="{{ $badgeStyleLarge }}">
															{{ $currentTier->name }}
														</span>
														@if($currentTier->discount_rate > 0)
															<span style="font-size: 11px; color: #28a745; font-weight: 600;">-{{ number_format($currentTier->discount_rate, 0) }}%</span>
														@endif
														<span style="font-size: 11px; color: #888;">•</span>
														<span style="font-size: 11px; color: #888;">
															Đã chi: <strong style="color: #333;">{{ number_format($totalSpent, 0, ',', '.') }} ₫</strong>
														</span>
													</div>
													@if($nextTierProgress)
														<div style="margin-top: 8px;">
															<div style="font-size: 11px; color: #666; margin-bottom: 4px;">
																Lên <strong>{{ $nextTierProgress['next_tier']->name }}</strong> còn: <strong style="color: #6777ef;">{{ number_format($nextTierProgress['remaining'], 0, ',', '.') }} ₫</strong>
															</div>
															<div style="background: #f0f0f0; border-radius: 4px; height: 6px; overflow: hidden;">
																<div style="{{ $progressStyle }}"></div>
															</div>
														</div>
													@endif
												</div>
											@endif
										</div>
									</div>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="{{ route('client.profile.index') }}">
										<i class="zmdi zmdi-account-circle me-2"></i>
										Hồ sơ cá nhân
									</a>
									<a class="dropdown-item" href="{{ route('client.order.list') }}">
										<i class="zmdi zmdi-shopping-cart me-2"></i>
										Đơn hàng của tôi
									</a>
									<a class="dropdown-item" href="{{ route('client.order.track') }}">
										<i class="zmdi zmdi-search me-2"></i>
										Tra cứu đơn hàng
									</a>
									<a class="dropdown-item" href="{{ route('client.profile.card') }}">
										<i class="zmdi zmdi-card me-2"></i>
										Ví của tôi
									</a>
									<a class="dropdown-item" href="{{ route('client.wishlist.index') }}">
										<i class="zmdi zmdi-favorite me-2"></i>
										Yêu thích
									</a>
									<div class="dropdown-divider"></div>
									<form method="POST" action="{{ route('logout') }}">
										@csrf
										<button type="submit" class="dropdown-item logout-btn">
											<i class="zmdi zmdi-power me-2"></i>
											Đăng xuất
										</button>
									</form>
								</div>
							</div>
						@else
							<!-- User chưa đăng nhập -->
							<a href="{{ route('loginView') }}" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
								<i class="zmdi zmdi-account"></i>
							</a>
						@endauth
					</div>
				</nav>
			</div>	
		</div>

		 <!-- Modal Search -->
		<div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
			<div class="container-search-header">
				<button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
					<img src="{{ asset('client/images/icons/icon-close2.png') }}" alt="CLOSE">
				</button>

				<form class="wrap-search-header flex-w p-l-15">
					<button class="flex-c-m trans-04">
						<i class="zmdi zmdi-search"></i>
					</button>
					<input class="plh3" type="text" name="search" placeholder="Search...">
				</form>
			</div>
		</div>

		@auth
		@push('scripts')
		@vite(['resources/js/app.js'])
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const notificationIcon = document.querySelector('.js-show-notifications');
			const notificationDropdown = document.querySelector('.notification-dropdown');
			const notificationList = document.querySelector('.notification-list');
			const markAllReadBtn = document.querySelector('.mark-all-read-btn');
			let isDropdownOpen = false;
			let isLoading = false;

			// Toggle dropdown
			if (notificationIcon) {
				notificationIcon.addEventListener('click', function(e) {
					e.stopPropagation();
					isDropdownOpen = !isDropdownOpen;
					if (isDropdownOpen) {
						notificationDropdown.style.display = 'block';
						loadNotifications();
					} else {
						notificationDropdown.style.display = 'none';
					}
				});

				// Đóng dropdown khi click bên ngoài
				document.addEventListener('click', function(e) {
					if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
						isDropdownOpen = false;
						notificationDropdown.style.display = 'none';
					}
				});
			}

			// Load notifications
			function loadNotifications() {
				if (isLoading) return;
				isLoading = true;
				
				fetch('{{ route("client.notifications.index") }}?limit=10', {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					}
				})
				.then(res => res.json())
				.then(data => {
					isLoading = false;
					renderNotifications(data.notifications);
					updateBadge(data.unread_count);
				})
				.catch(err => {
					isLoading = false;
					console.error('Error loading notifications:', err);
					notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Không thể tải thông báo</div>';
				});
			}

			// Render notifications
			function renderNotifications(notifications) {
				if (!notifications || notifications.length === 0) {
					notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Không có thông báo</div>';
					return;
				}

				let html = '';
				notifications.forEach(notif => {
					const isRead = notif.read_at !== null;
					const bgColor = isRead ? '#fff' : '#f8f9ff';
					html += `
						<div class="notification-item" data-id="${notif.id}" style="padding: 12px 15px; border-bottom: 1px solid #eee; background: ${bgColor}; cursor: pointer; transition: background 0.2s;">
							<div style="display: flex; align-items: start; gap: 10px;">
								<div style="flex: 1;">
									<div style="font-weight: ${isRead ? '400' : '600'}; font-size: 13px; color: #333; margin-bottom: 4px;">${escapeHtml(notif.title)}</div>
									<div style="font-size: 12px; color: #666; line-height: 1.4;">${escapeHtml(notif.message)}</div>
									<div style="font-size: 11px; color: #999; margin-top: 4px;">${notif.created_at_formatted || 'Vừa xong'}</div>
								</div>
								${!isRead ? '<div style="width: 8px; height: 8px; background: #6777ef; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>' : ''}
							</div>
						</div>
					`;
				});
				notificationList.innerHTML = html;

				// Add click handlers
				notificationList.querySelectorAll('.notification-item').forEach(item => {
					item.addEventListener('click', function() {
						const notifId = this.dataset.id;
						const notif = notifications.find(n => n.id == notifId);
						if (notif && notif.data && notif.data.url) {
							window.location.href = notif.data.url;
						}
					});
				});
			}

			// Update badge
			function updateBadge(count) {
				if (notificationIcon) {
					notificationIcon.setAttribute('data-notify', count);
					let badgeEl = notificationIcon.querySelector('.notification-badge');
					if (count > 0) {
						if (!badgeEl) {
							badgeEl = document.createElement('span');
							badgeEl.className = 'notification-badge';
							badgeEl.style.cssText = 'position: absolute; top: -5px; right: -5px; background: #ff4d4f; color: #fff; border-radius: 10px; padding: 2px 6px; font-size: 10px; font-weight: 600; min-width: 18px; text-align: center; z-index: 1;';
							notificationIcon.appendChild(badgeEl);
						}
						badgeEl.textContent = count > 99 ? '99+' : count;
						badgeEl.style.display = 'block';
					} else {
						if (badgeEl) badgeEl.style.display = 'none';
					}
				}
			}

			// Mark as read
			window.markAsRead = function(notifId) {
				fetch('{{ route("client.notifications.mark-read", ["id" => ":id"]) }}'.replace(':id', notifId), {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						updateBadge(data.unread_count);
						loadNotifications();
					}
				})
				.catch(err => console.error('Error marking as read:', err));
			};

			// Mark all as read
			if (markAllReadBtn) {
				markAllReadBtn.addEventListener('click', function(e) {
					e.stopPropagation();
					fetch('{{ route("client.notifications.mark-read") }}', {
						method: 'POST',
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'Accept': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						}
					})
					.then(res => res.json())
					.then(data => {
						if (data.success) {
							updateBadge(0);
							loadNotifications();
						}
					})
					.catch(err => console.error('Error marking all as read:', err));
				});
			}

			// Escape HTML
			function escapeHtml(text) {
				const div = document.createElement('div');
				div.textContent = text;
				return div.innerHTML;
			}

			// Listen for realtime notifications
			if (typeof window.Echo !== 'undefined') {
				const userId = {{ auth()->id() }};
				
				// Listen on private channel for notifications
				window.Echo.private(`user.${userId}.notifications`)
					.listen('.notification.created', (e) => {
						const notif = e.notification || e;
						// Show toast notification
						showNotificationToast(notif.title || 'Thông báo', notif.message || '', notif.data?.url);
						// Reload notifications
						if (isDropdownOpen) {
							loadNotifications();
						} else {
							loadNotifications(); // Update badge
						}
					});

				// Listen for order status updates
				window.Echo.private(`user.${userId}.orders`)
					.listen('.order.status.updated', (e) => {
						// Reload notifications when order status changes
						loadNotifications();
					});
			}

			// Show toast notification
			function showNotificationToast(title, message, url) {
				const toast = document.createElement('div');
				toast.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: #fff; padding: 14px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 99999; font-weight: 600; max-width: 350px; animation: slideInRight 0.3s ease; cursor: pointer;';
				toast.innerHTML = `
					<div style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">${escapeHtml(title)}</div>
					<div style="font-size: 12px; opacity: 0.95;">${escapeHtml(message)}</div>
				`;
				
				if (url) {
					toast.addEventListener('click', () => {
						window.location.href = url;
					});
				}

				document.body.appendChild(toast);

				setTimeout(() => {
					toast.style.opacity = '0';
					toast.style.transform = 'translateX(100%)';
					setTimeout(() => toast.remove(), 300);
				}, 5000);
			}

			// Initial load
			loadNotifications();
		});
		</script>
		@endpush
		@endauth