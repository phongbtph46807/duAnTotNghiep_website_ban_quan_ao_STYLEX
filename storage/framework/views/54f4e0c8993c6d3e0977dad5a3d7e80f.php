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

	// Global Add-to-cart AJAX (opt-in only). Intercepts form posting to /cart/add when data-ajax="1"
    // Stylish toast (top-right, dark theme)
    function showToast(message) {
        // Create container once
        var $container = $('#toast-container-stylex');
        if (!$container.length) {
            $container = $('<div id="toast-container-stylex"></div>').css({
                position:'fixed', top:'20px', right:'20px', zIndex: 99999,
                display:'flex', flexDirection:'column', gap:'10px', pointerEvents:'none'
            });
            $('body').append($container);
        }

        var $toast = $('<div class="toast-stylex"></div>').css({
            background:'linear-gradient(135deg, #111, #1c1c1c)', color:'#fff',
            border:'1px solid rgba(255,255,255,0.08)', borderRadius:'10px',
            boxShadow:'0 10px 25px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.04)',
            padding:'12px 14px 10px 12px', minWidth:'260px', maxWidth:'360px',
            display:'flex', alignItems:'flex-start', gap:'10px',
            transform:'translateX(20px)', opacity:0, pointerEvents:'auto'
        });

        var $iconWrap = $('<div></div>').css({
            width:'28px', height:'28px', borderRadius:'8px',
            background:'#2a2a2a', display:'flex', alignItems:'center', justifyContent:'center',
            boxShadow:'0 6px 12px rgba(0,0,0,0.35)'
        });
        var $icon = $('<span>✓</span>').css({ fontWeight:700, color:'#fff' });
        $iconWrap.append($icon);

        var $content = $('<div></div>').css({ flex:1 });
        var $title = $('<div></div>').text('Thành công').css({ fontSize:'13px', opacity:.9, marginBottom:'2px' });
        var $msg = $('<div></div>').text(message).css({ fontSize:'14px', fontWeight:600 });
        var $bar = $('<div></div>').css({
            position:'relative', height:'3px', borderRadius:'10px',
            background:'rgba(255,255,255,0.08)', marginTop:'8px', overflow:'hidden'
        });
        var $barFill = $('<div></div>').css({ height:'100%', width:'100%', background:'rgba(255,255,255,0.85)' });
        $bar.append($barFill);
        $content.append($title, $msg, $bar);

        var $close = $('<button aria-label="Đóng">×</button>').css({
            background:'transparent', color:'rgba(255,255,255,0.6)', border:'none', cursor:'pointer',
            fontSize:'18px', lineHeight:1, padding:0, marginLeft:'6px'
        }).on('click', function(){ dismiss(true); });

        $toast.append($iconWrap, $content, $close);
        $container.append($toast);

        // Animate in
        requestAnimationFrame(function(){
            $toast.css({ transition:'transform .25s ease, opacity .25s ease', transform:'translateX(0)', opacity:1 });
        });

        // Progress and auto-hide
        var duration = 2000; // ms
        var start = Date.now();
        var timer = setInterval(function(){
            var elapsed = Date.now() - start;
            var pct = Math.max(0, 1 - elapsed / duration);
            $barFill.css('width', (pct*100)+'%');
            if (pct <= 0) dismiss();
        }, 30);

        function dismiss(immediate){
            clearInterval(timer);
            if (immediate) { $toast.remove(); return; }
            $toast.css({ transform:'translateX(20px)', opacity:0 });
            setTimeout(function(){ $toast.remove(); }, 220);
        }
    }

    $(document).on('submit', 'form[data-ajax="1"][action$="/cart/add"]', function(e){
		e.preventDefault();
		var $form = $(this);
		var payload = $form.serialize() + '&ajax=1';
		$.ajax({
			url: $form.attr('action'),
			type: 'POST',
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
			data: payload
		}).done(function(res){
            if (!res || !res.success) { swal('Thông báo', 'Không thể thêm vào giỏ', 'error'); return; }
			var count = res.cart_count || 0;
			$('.icon-header-noti.js-show-cart').attr('data-notify', count);
			if (window.addHeaderCartItemFromResponse && res.cart_item) {
				window.addHeaderCartItemFromResponse(res.cart_item, count);
			}
            showToast('Đã thêm vào giỏ hàng');
		}).fail(function(){
			swal('Thông báo', 'Không thể thêm vào giỏ', 'error');
		});
		return false;
	});
	
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

	<!-- Mini-cart behaviors: add to DOM, delete handler -->
	<script>

	// Function to append/update an item in the mini-cart
	window.addHeaderCartItemFromResponse = function(cartItem, cartCount){
		var $list = $('#cartItems');
		$list.find('.header-cart-empty').remove();
		var found = false;
		$list.find('li.header-cart-item').each(function(){
			if (String($(this).data('cart-id')) === String(cartItem.id)) {
				$(this).find('.header-cart-item-info').text((cartItem.quantity||1) + ' x ' + new Intl.NumberFormat('vi-VN').format(cartItem.price||0) + ' ₫');
				found = true;
			}
		});
		if (!found) {
			var imageUrl = (cartItem.product && cartItem.product.default_image_url) ? cartItem.product.default_image_url : (cartItem.product && cartItem.product.thumbnail) ? cartItem.product.thumbnail : '';
			var attrs = [];
			if (cartItem.variant && cartItem.variant.size) attrs.push('Size: ' + cartItem.variant.size.name);
			if (cartItem.variant && cartItem.variant.color) attrs.push('Màu: ' + cartItem.variant.color.name);
			$list.append(
				'<li class="header-cart-item flex-w flex-t m-b-12" data-cart-id="'+ cartItem.id +'">' +
					'<div class="header-cart-item-img">\
						<img src="'+ imageUrl +'" alt="IMG" />\
					</div>' +
					'<div class="header-cart-item-txt p-t-8" style="flex:1;">' +
						'<a href="#" class="header-cart-item-name m-b-5 hov-cl1 trans-04">'+ (cartItem.product ? cartItem.product.name : 'Sản phẩm') +'</a>' +
						(attrs.length ? '<div class="stext-110" style="font-size:12px;color:#888">'+ attrs.join(' • ') +'</div>' : '') +
						'<span class="header-cart-item-info">'+ (cartItem.quantity||1) +' x '+ new Intl.NumberFormat('vi-VN').format(cartItem.price||0) + ' ₫</span>' +
					'</div>' +
					'<button class="delete-item" type="button" data-cart-id="'+ cartItem.id +'" title="Xóa" style="margin-left:auto; background: none; border: none; cursor: pointer; align-self:center;">\
						<i class="zmdi zmdi-close"></i>\
					</button>' +
				'</li>'
			);
		}
		// Recompute totals
		var total = 0; $('#cartItems .header-cart-item-info').each(function(){ var t=$(this).text().split(' x '); if(t.length===2){ var q=parseInt(t[0])||0; var p=parseInt((t[1]||'').replace(/[^0-9]/g,''))||0; total += q*p; }});
		$('#totalAmount').text(new Intl.NumberFormat('vi-VN').format(total) + ' ₫'); $('#cartFooter').show(); $('.icon-header-noti.js-show-cart').attr('data-notify', cartCount||0); $('#cartItemCount').text('(' + (cartCount||0) + ')');
	};

	// Delete item handler (centralized)
	$(document).on('click', '#cartItems .delete-item', function(e){
		e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
		var cartId = $(this).data('cart-id');
		var $btn = $(this);
		$.ajax({ url: '/cart/' + cartId, method: 'DELETE', data: { _token: $('meta[name="csrf-token"]').attr('content') }, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }} )
		.done(function(res){
			if (!res || !res.success) return;
			var $list = $('#cartItems');
			$btn.closest('li.header-cart-item').remove();
			var total = 0, count = 0;
			$('#cartItems .header-cart-item-info').each(function(){ var t=$(this).text().split(' x '); if(t.length===2){ var q=parseInt(t[0])||0; var p=parseInt((t[1]||'').replace(/[^0-9]/g,''))||0; total += q*p; count += q; }});
			if (!$list.find('li.header-cart-item').length) { $list.html('<li class="header-cart-empty" style="padding: 60px 20px; text-align: center; color: #999;">\
				<i class="zmdi zmdi-shopping-cart" style="font-size: 64px; opacity: 0.3;"></i>\
				<p style="margin-top: 20px; font-size: 16px;">Giỏ hàng trống</p>\
			</li>'); $('#cartFooter').hide(); }
			$('#totalAmount').text(new Intl.NumberFormat('vi-VN').format(total) + ' ₫'); $('.icon-header-noti.js-show-cart').attr('data-notify', count); $('#cartItemCount').text('(' + count + ')');
		});
	});
	</script>
	
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