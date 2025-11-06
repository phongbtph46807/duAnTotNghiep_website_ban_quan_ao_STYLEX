@extends('client.layout.layout')

@section('title', 'Giỏ Hàng - ' . env('APP_NAME'))

@section('content')

<!-- breadcrumb -->
<div class="container">
	<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
		<a href="{{ route('home') }}" class="stext-109 cl8 hov-cl1 trans-04">
			Trang Chủ
			<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
		</a>

		<span class="stext-109 cl4">
			Giỏ Hàng
		</span>
	</div>
</div>

<!-- Shoping Cart -->
<form class="bg0 p-t-75 p-b-85">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
				<div class="m-l-25 m-r--38 m-lr-0-xl">
					<div class="wrap-table-shopping-cart">
						<table class="table-shopping-cart">
							<tr class="table-head">
								<th class="column-1">Sản Phẩm</th>
								<th class="column-2"></th>
								<th class="column-3">Giá</th>
								<th class="column-4">Số Lượng</th>
								<th class="column-5">Tổng</th>
								<th class="column-6"></th>
							</tr>

							<tbody id="cartItems">
								@if(count($cartData) > 0)
									@foreach($cartData as $item)
									<tr class="table-row" data-cart-id="{{ $item['id'] }}">
										<td class="column-1">
											<div class="how-itemcart1">
												@php
													// Lấy image_url từ controller nếu có, nếu không thì fallback về cách cũ
													if (isset($item['image_url'])) {
														$imageUrl = $item['image_url'];
													} else {
														$firstImage = $item['product']->productImages->first();
														if ($firstImage) {
															// Check if image exists in uploads folder
															$imagePath = public_path($firstImage->image_path);
															$imageUrl = file_exists($imagePath) ? asset($firstImage->image_path) : asset('client/images/product-01.jpg');
														} else {
															$imageUrl = asset('client/images/product-01.jpg');
														}
													}
												@endphp
												<img src="{{ $imageUrl }}" alt="{{ $item['product']->name }}">
											</div>
										</td>
										<td class="column-2">
											<a href="{{ route('client.products.show', $item['product']->id) }}">{{ $item['product']->name }}</a>
											@if($item['size'] || $item['color'] || $item['texture'])
												<br>
												<small class="text-muted">
													@if($item['size']) Size: {{ $item['size'] }} @endif
													@if($item['size'] && $item['color']) | @endif
													@if($item['color']) Màu: {{ $item['color'] }} @endif
													@if(($item['size'] || $item['color']) && $item['texture']) | @endif
													@if($item['texture']) Chất liệu: {{ $item['texture'] }} @endif
												</small>
											@endif
										</td>
										<td class="column-3">{{ number_format($item['price'], 0, ',', '.') }} VNĐ</td>
										<td class="column-4">
											<div class="wrap-num-product flex-w m-l-auto m-r-0">
												<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
													<i class="fs-16 zmdi zmdi-minus"></i>
												</div>

												<input class="mtext-104 cl3 txt-center num-product" type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" data-cart-id="{{ $item['id'] }}">

												<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
													<i class="fs-16 zmdi zmdi-plus"></i>
												</div>
											</div>
										</td>
										<td class="column-5">
											{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VNĐ
										</td>
										<td class="column-6">
											<button class="btn-remove-cart" data-cart-id="{{ $item['id'] }}">
												<i class="zmdi zmdi-close"></i>
											</button>
										</td>
									</tr>
									@endforeach
								@else
									<tr>
										<td colspan="6" class="text-center" style="padding: 50px;">
											<p style="font-size: 18px; color: #888;">Giỏ hàng trống</p>
											<a href="{{ route('client.products.index') }}" class="stext-106 cl6 hov1 trans-04">
												Tiếp tục mua sắm
											</a>
										</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>

					<div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
						<div>
							<a href="{{ route('client.products.index') }}" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-10">
								Tiếp tục mua sắm
							</a>
						</div>
					</div>
				</div>
			</div>

			@if(count($cartData) > 0)
			<div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-lr-0-xl">
				<div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-r-0-lg ml-auto">
					<h4 class="mtext-109 cl2 p-b-30">
						Cart Totals
					</h4>

					<div class="flex-w flex-t bor12 p-b-13">
						<div class="size-208">
							<span class="stext-110 cl2">
								Tổng:
							</span>
						</div>

						<div class="size-209">
							<span class="mtext-110 cl2">
								{{ number_format($total, 0, ',', '.') }} VNĐ
							</span>
						</div>
					</div>

					<button class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-tb-10" style="width: 100%;">
						Thanh Toán
					</button>
				</div>
			</div>
			@endif
		</div>
	</div>
</form>

@endsection

@push('scripts')
<script>
	$(document).ready(function() {
		// Update quantity
		$('.num-product').on('change', function() {
			const cartId = $(this).data('cart-id');
			const quantity = $(this).val();
			
			$.ajax({
				url: '/cart/' + cartId,
				method: 'PUT',
				data: {
					quantity: quantity,
					_token: $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					location.reload();
				}
			});
		});
		
		// Remove item - Direct delete without confirmation
		$('.btn-remove-cart').on('click', function() {
			const cartId = $(this).data('cart-id');
			
			$.ajax({
				url: '/cart/' + cartId,
				method: 'DELETE',
				data: {
					_token: $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					location.reload();
				},
				error: function() {
					location.reload();
				}
			});
		});
	});
</script>
@endpush

