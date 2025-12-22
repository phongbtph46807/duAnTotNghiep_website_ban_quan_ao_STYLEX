<style>
    .review-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #eee;
    }
    .review-product {
        display: flex;
        gap: 15px;
        flex: 1;
    }
    .review-product img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #eee;
    }
    .review-product-info {
        flex: 1;
    }
    .review-product-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        margin-bottom: 5px;
    }
    .review-product-name a {
        color: #333;
        text-decoration: none;
    }
    .review-product-name a:hover {
        color: #6777ef;
    }
    .review-variant {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }
    .review-rating {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .review-rating .stars {
        color: #ffc107;
        font-size: 18px;
    }
    .review-rating .rating-text {
        font-size: 14px;
        color: #666;
        margin-left: 5px;
    }
    .review-content {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    .review-media {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    .review-media img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 1px solid #eee;
        transition: transform 0.2s;
    }
    .review-media img:hover {
        transform: scale(1.05);
    }
    .review-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #999;
    }
    .review-date {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .empty-reviews {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .empty-reviews i {
        font-size: 64px;
        opacity: 0.3;
        margin-bottom: 20px;
    }
</style>

<div class="card profile-card">
    <div class="profile-card-header">
        <h4 class="mb-0" style="font-weight: 600; color: #333;">
            <i class="ri-star-line me-2" style="color: #6777ef;"></i>Đánh giá của tôi
        </h4>
    </div>
    <div class="profile-card-body">
        @if(!isset($reviews) || $reviews->isEmpty())
            <div class="empty-reviews">
                <i class="ri-star-line"></i>
                <h5 style="color: #666; margin-bottom: 10px;">Chưa có đánh giá nào</h5>
                <p style="color: #999;">Bạn chưa đánh giá sản phẩm nào. Hãy đánh giá các sản phẩm đã mua để giúp người khác có thêm thông tin!</p>
                <a href="{{ route('client.profile.index', ['tab' => 'orders']) }}" class="btn btn-primary" style="margin-top: 20px; border-radius: 8px;">
                    <i class="ri-shopping-bag-line me-1"></i> Xem đơn hàng của tôi
                </a>
            </div>
        @else
            @foreach($reviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-product">
                            @php
                                $product = $review->product;
                                $productName = $product ? $product->name : ('Sản phẩm #' . $review->product_id);
                                $productImage = $product ? ($product->default_image_url ?? asset('client/images/product/product-01.jpg')) : asset('client/images/product/product-01.jpg');
                                $productSlug = $product ? $product->slug : '#';
                            @endphp
                            <img src="{{ $productImage }}" alt="{{ $productName }}">
                            <div class="review-product-info">
                                <div class="review-product-name">
                                    <a href="{{ route('client.products.show', $productSlug) }}">{{ $productName }}</a>
                                </div>
                                @if($review->productVariant)
                                    @php
                                        $variantParts = [];
                                        if($review->productVariant->size) {
                                            $variantParts[] = 'Size: ' . $review->productVariant->size->name;
                                        }
                                        if($review->productVariant->color) {
                                            $variantParts[] = 'Màu: ' . $review->productVariant->color->name;
                                        }
                                        if($review->productVariant->texture) {
                                            $variantParts[] = 'Chất liệu: ' . $review->productVariant->texture->name;
                                        }
                                    @endphp
                                    @if(!empty($variantParts))
                                        <div class="review-variant">{{ implode(' - ', $variantParts) }}</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="review-rating">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    @if($review->content)
                        <div class="review-content">
                            {{ $review->content }}
                        </div>
                    @endif

                    @if($review->media && count($review->media) > 0)
                        <div class="review-media">
                            @foreach($review->media as $media)
                                @if(is_array($media) && isset($media['url']))
                                    <img src="{{ asset('storage/' . $media['url']) }}" alt="Review image" onclick="openImageModal('{{ asset('storage/' . $media['url']) }}')">
                                @elseif(is_string($media))
                                    <img src="{{ asset('storage/' . $media) }}" alt="Review image" onclick="openImageModal('{{ asset('storage/' . $media) }}')">
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="review-meta">
                        <div class="review-date">
                            <i class="ri-time-line"></i>
                            {{ $review->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($review->order)
                            <div style="color: #999;">
                                Đơn hàng: <strong>{{ $review->order->code }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if($reviews->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:99999;align-items:center;justify-content:center;padding:20px;">
    <button type="button" onclick="closeImageModal()" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:32px;color:#fff;cursor:pointer;z-index:100000;">&times;</button>
    <img id="modalImage" src="" alt="Review image" style="max-width:90%;max-height:90%;object-fit:contain;border-radius:8px;">
</div>

<script>
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImage');
    if (modal && img) {
        img.src = imageSrc;
        modal.style.display = 'flex';
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Đóng modal khi click bên ngoài
document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Đóng modal bằng phím ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

