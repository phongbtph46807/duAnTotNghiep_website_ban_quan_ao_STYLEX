<!--===============================================================================================-->	
	<script src="<?php echo e(asset('client/vendor/jquery/jquery-3.2.1.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/animsition/js/animsition.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/bootstrap/js/popper.js')); ?>"></script>
	<script src="<?php echo e(asset('client/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/select2/select2.min.js')); ?>"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/daterangepicker/moment.min.js')); ?>"></script>
	<script src="<?php echo e(asset('client/vendor/daterangepicker/daterangepicker.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/slick/slick.min.js')); ?>"></script>
	<script src="<?php echo e(asset('client/js/slick-custom.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/parallax100/parallax100.js')); ?>"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/MagnificPopup/jquery.magnific-popup.min.js')); ?>"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/isotope/isotope.pkgd.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/sweetalert/sweetalert.min.js')); ?>"></script>
	<script>
		$('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

	// Add to cart functionality - now handled by cart.js
	
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/vendor/perfect-scrollbar/perfect-scrollbar.min.js')); ?>"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('client/js/main.js')); ?>"></script>
	<script src="<?php echo e(asset('client/js/cart.js')); ?>"></script>
	
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
<?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/partials/js/js.blade.php ENDPATH**/ ?>