@extends('client.layouts.app')

@section('title', 'Danh sách yêu thích - ' . env('APP_NAME'))

@section('content')
<!-- Product -->
<div class="bg0 m-t-23 p-b-140">
	<div class="container">
		<div class="p-b-10">
			<h3 class="ltext-103 cl5">
				Danh sách yêu thích
			</h3>
		</div>

		@if(isset($products) && $products->count() > 0)
		<div class="row" id="wishlist-grid">
			@foreach($products as $product)
			<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 wishlist-item" data-product-id="{{ $product->id }}">
				<!-- Block2 -->
				<div class="block2">
					<div class="block2-pic hov-img0">
						<img src="{{ $product->default_image_url }}" alt="{{ $product->name }}">
						<a href="{{ route('client.products.show', $product->id) }}" 
						   class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
							Xem chi tiết
						</a>
					</div>

					<div class="block2-txt flex-w flex-t p-t-14">
						<div class="block2-txt-child1 flex-col-l">
							<a href="{{ route('client.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
								{{ $product->name }}
							</a>

							<span class="stext-105 cl3 product-price">
								@if($product->price_sale && $product->price_sale < $product->price)
									<span class="sale-price">{{ number_format($product->price_sale, 0, ',', '.') }} ₫</span>
									<span class="original-price">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
								@else
									{{ number_format($product->price, 0, ',', '.') }} ₫
								@endif
							</span>
							
							@if(!$product->is_active)
							<span class="badge badge-secondary mt-2" style="padding: 4px 8px; font-size: 11px;">Hết hàng</span>
							@endif
						</div>

						<div class="block2-txt-child2 flex-r p-t-3">
							<a href="#" class="btn-addwish-b2 dis-block pos-relative js-remove-from-wishlist js-addedwish-b2" data-product-id="{{ $product->id }}" style="margin-right: 15px;">
								<img class="icon-heart1 dis-block trans-04" src="{{ asset('client/images/icons/icon-heart-01.png') }}" alt="ICON">
								<img class="icon-heart2 dis-block trans-04 ab-t-l" src="{{ asset('client/images/icons/icon-heart-02.png') }}" alt="ICON">
							</a>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>

		<div class="flex-w flex-c-m m-t-30">
			{{ $products->links('pagination::bootstrap-4') }}
		</div>

		@else
		<div class="text-center p-t-50 p-b-50">
			<i class="zmdi zmdi-favorite-outline cl2 m-b-20" style="font-size: 60px;"></i>
			<h4 class="mtext-105 cl2 p-b-15">Danh sách trống</h4>
			<p class="stext-115 cl6 p-b-30">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>

			<a href="{{ route('client.products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-auto" style="width: 220px;">
				Mua sắm ngay
			</a>
		</div>
		@endif
	</div>
</div>

<style>
	.product-price {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		flex-wrap: wrap;
	}
	.product-price .sale-price {
		font-weight: 600;
		color: #111;
	}
	.product-price .original-price {
		text-decoration: line-through;
		color: #dc3545;
		font-size: 0.95rem;
	}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý xóa sản phẩm khỏi wishlist
    document.querySelectorAll('.js-remove-from-wishlist').forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const row = this.closest('.wishlist-item');
            
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                return;
            }
            
            // Gửi request xóa
            fetch('{{ route("client.wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'removed') {
                    // Xóa row khỏi table
                    row.remove();
                    
                    // Kiểm tra nếu không còn sản phẩm nào, reload trang
                    const remainingItems = document.querySelectorAll('.wishlist-item');
                    if (remainingItems.length === 0) {
                        location.reload();
                    }
                    
                    // Hiển thị thông báo
                    alert(data.message);
                } else {
                    alert('Có lỗi xảy ra: ' + (data.message || 'Không thể xóa sản phẩm'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa sản phẩm');
            });
        });
    });
});
</script>
@endpush

@endsection