## Kiểm tra luồng thêm vào giỏ hàng

### Vấn đề chính:
1. **Form không submit AJAX** - Form có `data-ajax="1"` nhưng không được xử lý
2. **Xử lý form bị trùng** - Có 2 nơi xử lý form (inline script + product-form.js)
3. **Variant ID không được cập nhật** - Hidden input `variant_id` không được set trước khi submit

### Giải pháp:

**1. File product-form.js** (đã tạo)
- Xử lý submit form #add-to-cart-form
- Xử lý nút #btn-buy-now
- Gửi AJAX request đến `/cart/add` và `/cart/buy-now`

**2. Cần xóa xử lý form khỏi inline script**
- Dòng 1-50 trong @push('scripts') của detail.blade.php
- Giữ lại xử lý variant selection và quantity controls

**3. Đảm bảo thứ tự load script**
- product-variants.js (cập nhật variant_id)
- product-stock.js (hiển thị tồn kho)
- product-form.js (xử lý form submit)
- Inline script (event listeners)

### Kiểm tra:
1. Mở DevTools Console
2. Click chọn size/màu
3. Kiểm tra `$('input[name="variant_id"]').val()` có giá trị không
4. Click "Thêm vào giỏ"
5. Kiểm tra Network tab xem request có được gửi không

### Lỗi có thể gặp:
- Variant ID rỗng → Kiểm tra updateVariant() có được gọi không
- Request không gửi → Kiểm tra product-form.js có được load không
- 422 error → Backend không nhận được variant_id hoặc size_name/color_name
