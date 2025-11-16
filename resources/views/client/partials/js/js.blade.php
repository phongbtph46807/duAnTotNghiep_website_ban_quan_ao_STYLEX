<!--===============================================================================================-->
<script src="{{ asset('client/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/animsition/js/animsition.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('client/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/select2/select2.min.js') }}"></script>
<script>
	$(".js-select2").each(function() {
		$(this).select2({
			minimumResultsForSearch: 20,
			dropdownParent: $(this).next('.dropDownSelect2')
		});
	})
</script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('client/vendor/daterangepicker/daterangepicker.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('client/js/slick-custom.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/parallax100/parallax100.js') }}"></script>
<script>
	$('.parallax100').parallax100();
</script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
<script>
	$('.gallery-lb').each(function() { // the containers for all your galleries
		$(this).magnificPopup({
			delegate: 'a', // the selector for gallery item
			type: 'image',
			gallery: {
				enabled: true
			},
			mainClass: 'mfp-fade'
		});
	});
</script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/isotope/isotope.pkgd.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/sweetalert/sweetalert.min.js') }}"></script>

<script>
	// $('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
	// 	e.preventDefault();
	// });

	// $('.js-addwish-b2').each(function(){
	// 	var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
	// 	$(this).on('click', function(){
	// 		swal(nameProduct, "is added to wishlist !", "success");

	// 		$(this).addClass('js-addedwish-b2');
	// 		$(this).off('click');
	// 	});
	// });

	// $('.js-addwish-detail').each(function(){
	// 	var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

	// 	$(this).on('click', function(){
	// 		swal(nameProduct, "is added to wishlist !", "success");

	// 		$(this).addClass('js-addedwish-detail');
	// 		$(this).off('click');
	// 	});
	// });

	/*---------------------------------------------*/

	$('.js-addcart-detail').each(function() {
		var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
		$(this).on('click', function() {
			swal(nameProduct, "is added to cart !", "success");
		});
	});
</script>
<!--===============================================================================================-->
<script src="{{ asset('client/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script>
	$('.js-pscroll').each(function() {
		$(this).css('position', 'relative');
		$(this).css('overflow', 'hidden');
		var ps = new PerfectScrollbar(this, {
			wheelSpeed: 1,
			scrollingThreshold: 1000,
			wheelPropagation: false,
		});

		$(window).on('resize', function() {
			ps.update();
		})
	});
</script>
<!--===============================================================================================-->
<script src="{{ asset('client/js/main.js') }}"></script>

<!-- Custom JavaScript for User Dropdown -->
<script>
	// Dropdown functionality
	document.addEventListener('DOMContentLoaded', function() {
		const dropdownToggle = document.querySelector('.dropdown-toggle');
		const dropdownMenu = document.querySelector('.dropdown-menu');

		if (dropdownToggle && dropdownMenu) {
			// Toggle dropdown
			dropdownToggle.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				dropdownMenu.classList.toggle('show');
			});

			// Close dropdown when clicking outside
			document.addEventListener('click', function(e) {
				if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
					dropdownMenu.classList.remove('show');
				}
			});

			// Prevent dropdown from closing when clicking inside
			dropdownMenu.addEventListener('click', function(e) {
				e.stopPropagation();
			});

			// Handle dropdown item clicks
			const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
			dropdownItems.forEach(function(item) {
				item.addEventListener('click', function(e) {
					// If it's a logout button, let the form submit
					if (this.classList.contains('logout-btn') || this.querySelector('button.logout-btn')) {
						return;
					}
					// For other items, prevent default and close dropdown
					e.preventDefault();
					dropdownMenu.classList.remove('show');
				});
			});
		}
	});
</script>

<!-- Wishlist Script -->
<script>
	document.addEventListener('DOMContentLoaded', () => {
		// Lấy CSRF Token
		const csrfElement = document.querySelector('meta[name="csrf-token"]');
		if (!csrfElement) {
			console.error('Lỗi: Thẻ <meta name="csrf-token"> bị thiếu trong layout.');
			return;
		}
		const csrfToken = csrfElement.getAttribute('content');

		//Lấy URL 
		const toggleUrl = '{{ route("client.wishlist.toggle") }}';

		//Tìm icon đếm số lượng (Icon này phải có ID)
		const counterIcon = document.getElementById('wishlist-counter-icon');

		//Gắn sự kiện click
		document.addEventListener('click', async function(event) {

			// Bắt sự kiện cho nút Tim (Thêm/Xóa)
			const wishlistButton = event.target.closest('.btn-wishlist');
			// Bắt sự kiện cho nút Xóa 
			const removeButton = event.target.closest('.js-remove-from-wishlist');

			if (wishlistButton) {
				await handleWishlistToggle(wishlistButton);
			}

			if (removeButton) {
				await handleWishlistRemove(removeButton);
			}
		});

		// HÀM XỬ LÝ NÚT TIM (Thêm/Xóa)
		async function handleWishlistToggle(button) {
			const productId = button.getAttribute('data-product-id');
			const icon = button.querySelector('i');

			try {
				const response = await fetch(toggleUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': csrfToken,
					},
					body: JSON.stringify({
						product_id: productId
					}),
				});

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					if (response.status === 401) {
						swal("Lỗi!", "Vui lòng đăng nhập để thực hiện chức năng này.", "error");
					} else {
						swal("Lỗi!", errorData.message || `Lỗi: ${response.status}`, "error");
					}
					return;
				}

				const result = await response.json();

				// Hiển thị thông báo (có tên sản phẩm)
				swal(result.message, {
					icon: "success",
					timer: 2000,
					buttons: false
				});

				// Đếm số lượng và cập nhật icon
				if (counterIcon) {
					counterIcon.setAttribute('data-notify', result.newCount);
				}

				// Cập nhật giao diện nút bấm
				if (result.status === 'added') {
					button.classList.add('active-wishlist');
					button.setAttribute('data-tooltip', 'Xóa khỏi yêu thích');
					icon.classList.remove('zmdi-favorite-outline');
					icon.classList.add('zmdi-favorite');
				} else if (result.status === 'removed') {
					button.classList.remove('active-wishlist');
					button.setAttribute('data-tooltip', 'Thêm vào yêu thích');
					icon.classList.remove('zmdi-favorite');
					icon.classList.add('zmdi-favorite-outline');
				}

			} catch (error) {
				console.error('Lỗi Wishlist:', error);
				swal("Lỗi!", "Không thể kết nối đến máy chủ.", "error");
			}
		}

		// HÀM XỬ LÝ NÚT XÓA 
		async function handleWishlistRemove(button) {
			const productId = button.getAttribute('data-product-id');
			const row = button.closest('.wishlist-item-row');

			// Hỏi xác nhận
			swal({
					title: "Bạn có chắc chắn?",
					text: "Sản phẩm này sẽ bị xóa khỏi danh sách yêu thích.",
					icon: "warning",
					buttons: ["Hủy", "Đồng ý Xóa"],
					dangerMode: true,
				})
				.then(async (willDelete) => {
					if (willDelete) {
						try {
							const response = await fetch(toggleUrl, {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'X-CSRF-TOKEN': csrfToken
								},
								body: JSON.stringify({
									product_id: productId
								}),
							});

							if (!response.ok) throw new Error('Network response was not ok');

							const result = await response.json();

							if (result.status === 'removed') {
								// Hiển thị thông báo 
								swal("Đã xóa!", result.message, "success");

								// Đếm số lượng và cập nhật icon
								if (counterIcon) {
									counterIcon.setAttribute('data-notify', result.newCount);
								}

								// Xóa hàng khỏi DOM
								if (row) {
									row.style.transition = 'opacity 0.5s ease';
									row.style.opacity = '0';
									setTimeout(() => {
										row.remove();
										// Kiểm tra nếu bảng trống
										if (document.querySelectorAll('.wishlist-item-row').length === 0) {
											document.querySelector('.wrap-table-shopping-cart').innerHTML =
												'<div class="text-center p-t-30 p-b-30" style="border: 1px dashed #ccc;"><p class="stext-107 cl6">Danh sách yêu thích của bạn đang trống.</p></div>';
										}
									}, 500);
								}
							} else {
								swal("Lỗi!", "Không thể xóa, vui lòng thử lại.", "error");
							}
						} catch (error) {
							swal("Lỗi!", "Đã xảy ra lỗi khi kết nối.", "error");
						}
					}
				});
		}
	});
</script>