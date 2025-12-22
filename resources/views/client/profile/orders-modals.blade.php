{{-- Modal hủy đơn --}}
<div id="cancelModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;padding:15px;">
    <div style="background:#fff;border-radius:12px;padding:20px;max-width:520px;width:100%;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
        <button type="button" id="closeCancelModal" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>
        <h5 style="margin-bottom:16px;font-weight:700;">Hủy đơn hàng</h5>
        <p id="cancelOrderCode" style="margin-top:-6px;color:#666;font-size:13px;"></p>

        <form id="cancelForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-weight:600;display:block;margin-bottom:6px;">Lý do hủy *</label>
                <textarea name="cancel_reason" class="co-textarea" rows="3" placeholder="Ví dụ: Thay đổi địa chỉ, đặt nhầm sản phẩm..." required></textarea>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-weight:600;display:block;margin-bottom:6px;">Ảnh minh họa (tùy chọn, tối đa 3 ảnh)</label>
                <input type="file" id="cancelImagesInput" name="cancel_images[]" accept="image/*" multiple style="width:100%;">
                <small style="color:#777;">Tối đa 3 ảnh, mỗi ảnh 2MB.</small>
                <div id="cancelPreview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;"></div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
                <button type="button" id="cancelModalCloseBtn" style="padding:8px 14px;background:#f0f0f0;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Đóng</button>
                <button type="submit" style="padding:8px 14px;background:#ff4d4f;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;">Xác nhận hủy</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal trả hàng --}}
<div id="returnModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;padding:15px;">
    <div style="background:#fff;border-radius:12px;padding:20px;max-width:520px;width:100%;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
        <button type="button" id="closeReturnModal" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>
        <h5 style="margin-bottom:16px;font-weight:700;">Yêu cầu trả hàng</h5>
        <p id="returnOrderCode" style="margin-top:-6px;color:#666;font-size:13px;"></p>

        <form id="returnForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-weight:600;display:block;margin-bottom:6px;">Lý do trả hàng *</label>
                <textarea name="return_reason" class="co-textarea" rows="3" placeholder="Ví dụ: Sản phẩm lỗi, thiếu phụ kiện, giao nhầm..." required></textarea>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-weight:600;display:block;margin-bottom:6px;">Ảnh minh họa (tùy chọn, tối đa 3 ảnh)</label>
                <input type="file" id="returnImagesInput" name="return_images[]" accept="image/*" multiple style="width:100%;">
                <small style="color:#777;">Tối đa 3 ảnh, mỗi ảnh 2MB.</small>
                <div id="returnPreview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;"></div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
                <button type="button" id="returnModalCloseBtn" style="padding:8px 14px;background:#f0f0f0;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Đóng</button>
                <button type="submit" style="padding:8px 14px;background:#f59e0b;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;">Gửi yêu cầu</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal đánh giá sản phẩm --}}
<div id="reviewModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:500px;width:90%;max-height:90vh;overflow-y:auto;position:relative;">
        <button type="button" id="closeReviewModal" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>
        <h5 style="margin-bottom:20px;font-weight:600;">Đánh giá sản phẩm</h5>
        
        <form id="reviewForm" method="POST" action="{{ route('client.order.review') }}">
            @csrf
            <input type="hidden" name="order_id" id="reviewOrderId">
            <input type="hidden" name="order_item_id" id="reviewItemId">
            <input type="hidden" name="variant_id" id="reviewVariantId">
            <input type="hidden" name="rating" id="reviewRating">
            
            <div style="margin-bottom:16px;">
                <strong id="reviewProductName"></strong>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Đánh giá của bạn:</label>
                <div class="star-rating-review" id="starRatingReview" style="display:flex;gap:4px;">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star-review" data-rating="{{ $i }}" style="font-size:24px;color:#ddd;cursor:pointer;">★</span>
                    @endfor
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Nội dung đánh giá (tùy chọn):</label>
                <textarea name="content" id="reviewContent" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:14px;resize:vertical;"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <button type="button" id="closeReviewModalBtn" style="padding:8px 16px;background:#f0f0f0;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Đóng</button>
                <button type="submit" id="submitReviewBtn" style="padding:8px 16px;background:#6777ef;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Gửi đánh giá</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@vite(['resources/js/app.js'])
<script>
// Cancel modal
(function(){
    const cancelModal = document.getElementById('cancelModal');
    if (!cancelModal) return;

    const form = document.getElementById('cancelForm');
    const codeEl = document.getElementById('cancelOrderCode');

    function openModal(orderId, orderCode) {
        if (form) {
            form.action = "{{ url('/order') }}/" + orderId + "/cancel";
        }
        if (codeEl) {
            codeEl.textContent = orderCode ? ('Mã đơn: ' + orderCode) : '';
        }
        cancelModal.style.display = 'flex';
    }

    function closeModal() {
        cancelModal.style.display = 'none';
    }

    document.querySelectorAll('.btn-cancel-order').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const orderId = btn.dataset.orderId;
            const orderCode = btn.dataset.orderCode;
            openModal(orderId, orderCode);
        });
    });

    document.getElementById('closeCancelModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelModalCloseBtn')?.addEventListener('click', closeModal);
    cancelModal.addEventListener('click', (e) => {
        if (e.target === cancelModal) closeModal();
    });
})();

// Return modal
(function(){
    const modal = document.getElementById('returnModal');
    if (!modal) return;
    const form = document.getElementById('returnForm');
    const codeEl = document.getElementById('returnOrderCode');

    function openModal(orderId, orderCode) {
        if (form) form.action = "{{ url('/order') }}/" + orderId + "/return";
        if (codeEl) codeEl.textContent = orderCode ? ('Mã đơn: ' + orderCode) : '';
        modal.style.display = 'flex';
    }
    function closeModal() { modal.style.display = 'none'; }

    document.querySelectorAll('.btn-return-order').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            openModal(btn.dataset.orderId, btn.dataset.orderCode);
        });
    });
    document.getElementById('closeReturnModal')?.addEventListener('click', closeModal);
    document.getElementById('returnModalCloseBtn')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
})();

// Preview images
(function(){
    function bindPreview(inputId, previewId, maxFiles = 3) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            const files = Array.from(input.files || []);
            if (files.length > maxFiles) {
                alert(`Chỉ được chọn tối đa ${maxFiles} ảnh.`);
                input.value = '';
                preview.innerHTML = '';
                return;
            }
            preview.innerHTML = '';
            files.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #eee';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    bindPreview('cancelImagesInput', 'cancelPreview');
    bindPreview('returnImagesInput', 'returnPreview');
})();

// Review modal
(function() {
    let currentOrderId = null;
    let currentItemId = null;
    let selectedRating = 0;
    let reviewModal = null;

    function openModal() {
        if (reviewModal) {
            reviewModal.style.display = 'flex';
        }
    }

    function closeModal() {
        if (reviewModal) {
            reviewModal.style.display = 'none';
        }
    }

    function highlightStars(rating) {
        document.querySelectorAll('.star-review').forEach(function(star, index) {
            if (index < rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#ddd';
            }
        });
    }

    function resetStars() {
        document.querySelectorAll('.star-review').forEach(function(star) {
            star.style.color = '#ddd';
        });
    }

    function initReviewModal() {
        reviewModal = document.getElementById('reviewModal');
        if (!reviewModal) {
            console.error('Review modal not found');
            return;
        }

        const reviewButtons = document.querySelectorAll('.btn-review-item');
        reviewButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                currentOrderId = this.dataset.orderId;
                currentItemId = this.dataset.itemId;
                const variantId = this.dataset.variantId || '';
                document.getElementById('reviewVariantId').value = variantId;
                const productNameEl = document.getElementById('reviewProductName');
                if (productNameEl) {
                    productNameEl.textContent = this.dataset.productName || 'Sản phẩm';
                }
                selectedRating = 0;
                resetStars();
                const contentEl = document.getElementById('reviewContent');
                if (contentEl) {
                    contentEl.value = '';
                }
                openModal();
            });
        });

        const closeBtn1 = document.getElementById('closeReviewModal');
        const closeBtn2 = document.getElementById('closeReviewModalBtn');
        if (closeBtn1) closeBtn1.addEventListener('click', closeModal);
        if (closeBtn2) closeBtn2.addEventListener('click', closeModal);
        
        reviewModal.addEventListener('click', function(e) {
            if (e.target === reviewModal) {
                closeModal();
            }
        });

        document.querySelectorAll('.star-review').forEach(function(star) {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
            });
            
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                highlightStars(selectedRating);
            });
        });

        const starRatingEl = document.getElementById('starRatingReview');
        if (starRatingEl) {
            starRatingEl.addEventListener('mouseleave', function() {
                highlightStars(selectedRating);
            });
        }

        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                if (selectedRating === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn số sao đánh giá!');
                    return false;
                }

                document.getElementById('reviewOrderId').value = currentOrderId;
                document.getElementById('reviewItemId').value = currentItemId;
                document.getElementById('reviewRating').value = selectedRating;

                const submitBtn = document.getElementById('submitReviewBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Đang gửi...';
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReviewModal);
    } else {
        initReviewModal();
    }
})();
</script>
@endpush

