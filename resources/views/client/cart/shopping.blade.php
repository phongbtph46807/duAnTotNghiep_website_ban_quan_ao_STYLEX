@extends('client.layout.layout')

@section('title', 'Giỏ hàng - ' . env('APP_NAME'))

@section('content')

<div class="container">
	<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
		<a href="{{ route('home') }}" class="stext-109 cl8 hov-cl1 trans-04">
			Trang Chủ
			<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
		</a>
		<span class="stext-109 cl4">Giỏ Hàng</span>
	</div>
</div>

<section class="bg0 p-t-40 p-b-60">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
				<div class="m-l-25 m-r--38 m-lr-0-xl">
					<style>
						/* Harmonize cart table columns */
						.table-shopping-cart .table_head th { padding: 14px 12px; }
						.table-shopping-cart .column-1 { width: 120px; }
						.table-shopping-cart .column-2 { width: auto; padding-left: 16px; }
						.table-shopping-cart .column-3 { width: 140px; text-align: center; }
						.table-shopping-cart .column-4 { width: 160px; text-align: center; }
						.table-shopping-cart .column-5 { width: 150px; text-align: right; }
						.table-shopping-cart .column-6 { width: 100px; text-align: center; }

						/* Align body cells same as headers */
						.table-shopping-cart td.column-3,
						.table-shopping-cart td.column-4,
						.table-shopping-cart td.column-6 { text-align: center; }
						.table-shopping-cart td.column-5 { text-align: right; }

						/* Tidy action button */
						.table-shopping-cart .delete-line { color: #999; }
						.table-shopping-cart .delete-line:hover { color: #333; }

						/* Product name readability */
						.table-shopping-cart td.column-2 a.stext-104 {
							display: -webkit-box;
							-webkit-line-clamp: 2;
							-webkit-box-orient: vertical;
							overflow: hidden;
							max-width: 420px;
							line-height: 1.35;
						}
					</style>
					<div class="wrap-table-shopping-cart">
						<table class="table-shopping-cart">
							<tr class="table_head">
								<th class="column-1">Sản phẩm</th>
								<th class="column-2">Tên</th>
								<th class="column-3">Giá</th>
								<th class="column-4">Số lượng</th>
								<th class="column-5">Tạm tính</th>
								<th class="column-6">Thao tác</th>
							</tr>

							@php /* grand from controller-provided $total; avoid heavy PHP in Blade */ @endphp
							@if(!empty($cartData) && count($cartData))
								@foreach($cartData as $item)
								@php
									$img = $item['image_url'] ?? ($item['product']->default_image_url ?? asset('client/images/product-01.jpg'));
									$qty = (int)($item['quantity'] ?? 1);
									$price = (float)($item['price'] ?? 0);
									$line = (float)($item['line_total'] ?? ($qty * $price));
									$varObj = $item['variant'] ?? null;
									$sizeName = $item['size'] ?? ($varObj && isset($varObj->size) ? ($varObj->size->name ?? null) : null);
									$colorName = $item['color'] ?? ($varObj && isset($varObj->color) ? ($varObj->color->name ?? null) : null);
								@endphp
								<tr class="table_row align-middle" data-cart-id="{{ $item['id'] }}" data-price="{{ $price }}" data-qty="{{ $qty }}">
									<td class="column-1">
										<div class="how-itemcart1">
											<img src="{{ $img }}" alt="IMG">
										</div>
									</td>
									<td class="column-2">
										<a href="{{ route('client.products.show', $item['product']->id) }}" class="stext-104 cl4 hov-cl1 trans-04">{{ $item['product']->name }}</a>
								@if($sizeName || $colorName)
									<div class="stext-110" style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">
										@if($sizeName)
											<span style="background:#f3f3f3; color:#333; border:1px solid #e5e5e5; border-radius:12px; padding:2px 8px; font-size:12px;">Size: {{ $sizeName }}</span>
										@endif
										@if($colorName)
											<span style="background:#f3f3f3; color:#333; border:1px solid #e5e5e5; border-radius:12px; padding:2px 8px; font-size:12px;">Màu: {{ $colorName }}</span>
										@endif
									</div>
								@endif
									</td>
									<td class="column-3">{{ number_format($price, 0, ',', '.') }} ₫</td>
									<td class="column-4">
										<div class="wrap-num-product flex-w m-l-auto m-r-0">
											<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m" data-action="dec" data-cart-id="{{ $item['id'] }}">
												<i class="fs-16 zmdi zmdi-minus"></i>
											</div>
											<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product-{{ $item['id'] }}" value="{{ $qty }}" min="1" data-cart-id="{{ $item['id'] }}">
											<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m" data-action="inc" data-cart-id="{{ $item['id'] }}">
												<i class="fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>
									</td>
									<td class="column-5 line-total">{{ number_format($line, 0, ',', '.') }} ₫</td>
									<td class="column-6">
										<button type="button" class="delete-line" data-cart-id="{{ $item['id'] }}" title="Xóa" style="background:none;border:none;cursor:pointer;">
											<i class="zmdi zmdi-close"></i>
										</button>
									</td>
								</tr>
								@endforeach
							@else
								<tr>
									<td colspan="5" class="text-center p-tb-40">
										<p class="stext-106 cl6">Giỏ hàng trống</p>
										<a href="{{ route('client.products.index') }}" class="stext-106 cl6 hov1 trans-04">Tiếp tục mua sắm</a>
									</td>
								</tr>
							@endif
						</table>
					</div>

						<div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
						<div class="flex-w flex-m m-r-20 m-tb-5" style="flex: 1;">
							<input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" 
								   type="text" 
								   id="voucherCode" 
								   name="coupon" 
								   placeholder="Nhập mã voucher"
								   style="flex: 1;">
							<button type="button" 
									id="applyVoucherBtn" 
									class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
								Áp dụng mã
							</button>
						</div>
						<div id="voucherMessage" class="w-full m-tb-5" style="font-size: 12px; color: #28a745;"></div>
						<div id="voucherInfo" class="w-full m-tb-5" style="display: none;">
							<span class="stext-110 cl2" style="color: #28a745; font-weight: 600;">
								Mã: <span id="appliedVoucherCode"></span>
							</span>
							<button type="button" 
									id="removeVoucherBtn" 
									class="stext-110 cl2 ml-2" 
									style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: underline;">
								(Xóa)
							</button>
						</div>
						<div id="update-cart-btn" class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">Cập nhật giỏ hàng</div>
					</div>
				</div>
			</div>

			<div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
				<div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
					<h4 class="mtext-109 cl2 p-b-30">Tổng cộng</h4>
					<div class="flex-w flex-t bor12 p-b-13">
						<div class="size-208"><span class="stext-110 cl2">Tạm tính:</span></div>
						<div class="size-209"><span class="mtext-110 cl2" id="cart-subtotal">{{ number_format($total ?? 0, 0, ',', '.') }} ₫</span></div>
					</div>
					<div class="flex-w flex-t bor12 p-b-13" id="discountRow" style="display: none;">
						<div class="size-208"><span class="stext-110 cl2" style="color: #28a745;">Giảm giá:</span></div>
						<div class="size-209"><span class="mtext-110 cl2" id="discountAmount" style="color: #28a745;">0 ₫</span></div>
					</div>
					<div class="flex-w flex-t bor12 p-t-15 p-b-30">
						<div class="size-208 w-full-ssm"><span class="stext-110 cl2">Vận chuyển:</span></div>
						<div class="size-209 p-r-18 p-r-0-sm w-full-ssm"><span class="stext-111 cl6 p-t-2">Tính khi thanh toán</span></div>
					</div>
					<div class="flex-w flex-t p-t-27 p-b-33">
						<div class="size-208"><span class="mtext-101 cl2">Tổng:</span></div>
						<div class="size-209 p-t-1"><span class="mtext-110 cl2" id="cart-grandtotal">{{ number_format($total ?? 0, 0, ',', '.') }} ₫</span></div>
					</div>
						<a href="{{ route('client.checkout.index') }}" class="flex-c-m stext-101 cl0 size-116 bg1 bor14 hov-btn3 p-lr-15 trans-04 pointer">Thanh toán</a>
				</div>
			</div>
		</div>
	</div>
</section>

@push('scripts')
<script>
(function($){
	function format(n){ try { return new Intl.NumberFormat('vi-VN').format(n) + ' ₫'; } catch(e){ return n + ' ₫'; } }
	var currentDiscount = parseFloat('{{ $discount ?? 0 }}') || 0;
	var currentSubtotal = 0;
	var appliedVoucher = {!! json_encode($voucher ?? null) !!};

	// Initialize voucher UI if voucher is applied
	$(document).ready(function(){
		if (appliedVoucher) {
			currentDiscount = parseFloat('{{ $discount ?? 0 }}') || 0;
			$('#voucherInfo').show();
			$('#appliedVoucherCode').text(appliedVoucher.code);
			$('#voucherCode').val('').prop('disabled', true);
			$('#applyVoucherBtn').hide();
			updateGrandTotal();
		}
	});

	function recalcTotals(){
		var grand = 0;
		$('table.table-shopping-cart tr.table_row').each(function(){
			var $row = $(this);
			var price = parseFloat($row.data('price')||0);
			var qty = parseInt($row.find('input.num-product').val(), 10);
			if (isNaN(qty) || qty < 1) qty = 1;
			var line = price * qty;
			$row.find('.line-total').text(format(line));
			grand += line;
		});
		currentSubtotal = grand;
		$('#cart-subtotal').text(format(grand));
		
		// Recalculate discount if voucher is applied
		if (appliedVoucher) {
			if (appliedVoucher.type === 'percent') {
				currentDiscount = (grand * appliedVoucher.value) / 100;
			} else if (appliedVoucher.type === 'fixed') {
				currentDiscount = appliedVoucher.value;
				if (currentDiscount > grand) {
					currentDiscount = grand;
				}
			}
		}
		
		updateGrandTotal();
	}

	function updateGrandTotal(){
		var finalTotal = currentSubtotal - currentDiscount;
		if (finalTotal < 0) finalTotal = 0;
		$('#cart-grandtotal').text(format(finalTotal));
		if (currentDiscount > 0) {
			$('#discountRow').show();
			$('#discountAmount').text(format(currentDiscount));
		} else {
			$('#discountRow').hide();
		}
	}
	// Mark dirty on +/- and change, do not call server
	$(document).on('click', 'table.table-shopping-cart .btn-num-product-up, table.table-shopping-cart .btn-num-product-down', function(e){
		e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
		var $btn = $(this);
		var $row = $btn.closest('tr.table_row');
		var $input = $row.find('input.num-product');
		setTimeout(function(){
			var current = parseInt($input.val(), 10);
			if (isNaN(current) || current < 1) { current = 1; $input.val(current); }
			$row.addClass('dirty');
		}, 0);
		return false;
	});
	$(document).on('change', 'table.table-shopping-cart input.num-product', function(e){
		e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
		var $input = $(this);
		var $row = $input.closest('tr.table_row');
		var val = parseInt($input.val(), 10);
		if (isNaN(val) || val < 1) { val = 1; $input.val(val); }
		$row.addClass('dirty');
	});
	// Update button: only recompute totals and commit values client-side
	$(document).on('click', '#update-cart-btn', function(e){
		e.preventDefault();
		var rows = $('table.table-shopping-cart tr.table_row.dirty');
		if (!rows.length) {
			if (typeof showToast === 'function') { showToast('Không có thay đổi số lượng'); }
			return;
		}
		var ajaxCount = 0, failCount = 0;
		rows.each(function(){
			var $row = $(this);
			var qty = parseInt($row.find('input.num-product').val(), 10);
			if (isNaN(qty) || qty < 1) qty = 1;
			var cartId = $row.data('cart-id');
			ajaxCount++;
			$.ajax({
				url: '/cart/'+cartId,
				method: 'PUT',
				headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
				data: { quantity: qty, _token: $('meta[name="csrf-token"]').attr('content') }
			}).done(function(res){
				// ... nothing to do here per item ...
			}).fail(function(){ failCount++; })
			.always(function(){
				ajaxCount--;
				if (ajaxCount===0) {
					rows.removeClass('dirty').attr('data-qty', function(){ return $(this).find('input.num-product').val(); });
					recalcTotals();
					if (failCount === 0) {
						if (typeof showToast === 'function') { showToast('Đã cập nhật giỏ hàng'); }
					} else {
						if (typeof swal === 'function') { swal('Thông báo', 'Có lỗi với '+failCount+' sản phẩm khi cập nhật', 'warning'); }
					}
				}
			});
		});
	});
	// Initial totals
	recalcTotals();

	// Apply voucher
	$(document).on('click', '#applyVoucherBtn', function(e){
		e.preventDefault();
		var code = $('#voucherCode').val().trim();
		if (!code) {
			$('#voucherMessage').text('Vui lòng nhập mã voucher').css('color', '#dc3545');
			return;
		}
		$('#applyVoucherBtn').prop('disabled', true).text('Đang xử lý...');
		$('#voucherMessage').text('');
		$.ajax({
			url: '/cart/voucher/apply',
			method: 'POST',
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
			data: { code: code, _token: $('meta[name="csrf-token"]').attr('content') }
		}).done(function(res){
			if (res.success) {
				currentDiscount = res.discount;
				updateGrandTotal();
				$('#voucherMessage').text(res.message).css('color', '#28a745');
				$('#voucherInfo').show();
				$('#appliedVoucherCode').text(res.voucher.code);
				$('#voucherCode').val('').prop('disabled', true);
				$('#applyVoucherBtn').hide();
			} else {
				$('#voucherMessage').text(res.message || 'Có lỗi xảy ra').css('color', '#dc3545');
			}
		}).fail(function(xhr){
			var msg = 'Có lỗi xảy ra';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				msg = xhr.responseJSON.message;
			}
			$('#voucherMessage').text(msg).css('color', '#dc3545');
		}).always(function(){
			$('#applyVoucherBtn').prop('disabled', false).text('Áp dụng mã');
		});
	});

	// Remove voucher
	$(document).on('click', '#removeVoucherBtn', function(e){
		e.preventDefault();
		$.ajax({
			url: '/cart/voucher/remove',
			method: 'POST',
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
			data: { _token: $('meta[name="csrf-token"]').attr('content') }
		}).done(function(res){
			if (res.success) {
				currentDiscount = 0;
				updateGrandTotal();
				$('#voucherMessage').text(res.message).css('color', '#28a745');
				$('#voucherInfo').hide();
				$('#voucherCode').val('').prop('disabled', false);
				$('#applyVoucherBtn').show();
				setTimeout(function(){
					$('#voucherMessage').text('');
				}, 3000);
			}
		});
	});

	// Enter key to apply voucher
	$(document).on('keypress', '#voucherCode', function(e){
		if (e.which === 13) {
			$('#applyVoucherBtn').click();
		}
	});

	// Delete line from main cart (AJAX)
	$(document).on('click', 'table.table-shopping-cart .delete-line', function(e){
		e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
		var $btn = $(this);
		var cartId = $btn.data('cart-id');
		$.ajax({ url: '/cart/' + cartId, method: 'DELETE', data: { _token: $('meta[name="csrf-token"]').attr('content') }, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }})
		.done(function(res){
			if (!res || !res.success) return;
			$btn.closest('tr.table_row').remove();
			recalcTotals();
			if (typeof res.cart_count !== 'undefined') {
				$('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
			}
		});
		return false;
	});
})(jQuery);
</script>
@endpush

@endsection
