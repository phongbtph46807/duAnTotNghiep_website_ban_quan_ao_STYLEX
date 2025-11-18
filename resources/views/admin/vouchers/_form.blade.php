@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Mã voucher <span class="text-danger">*</span></label>
        <input type="text" name="code" value="{{ old('code', $voucher->code ?? '') }}" class="form-control @error('code') is-invalid @enderror" required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Loại <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="percent" {{ old('type', $voucher->type ?? 'percent') === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
            <option value="fixed" {{ old('type', $voucher->type ?? '') === 'fixed' ? 'selected' : '' }}>Cố định (₫)</option>
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Giá trị <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $voucher->value ?? 0) }}" class="form-control @error('value') is-invalid @enderror" required>
        @error('value')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Giảm tối đa (áp dụng với %)</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $voucher->max_discount_amount ?? '') }}" class="form-control @error('max_discount_amount') is-invalid @enderror" placeholder="Không giới hạn nếu bỏ trống">
        @error('max_discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Chỉ áp dụng khi loại là phần trăm</small>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Đơn tối thiểu</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount ?? '') }}" class="form-control @error('min_order_amount') is-invalid @enderror" placeholder="Không bắt buộc">
        @error('min_order_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Đơn hàng tối thiểu để áp dụng voucher</small>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Giới hạn lượt dùng</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}" class="form-control @error('usage_limit') is-invalid @enderror" placeholder="Không giới hạn nếu bỏ trống">
        @error('usage_limit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Số lượt sử dụng tối đa</small>
    </div>

    <div class="col-md-12">
        <label class="form-label fw-semibold">Mô tả</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Nhập mô tả voucher...">{{ old('description', $voucher->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Bắt đầu</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($voucher->starts_at) ? $voucher->starts_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('starts_at') is-invalid @enderror">
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Kết thúc</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($voucher->ends_at) ? $voucher->ends_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('ends_at') is-invalid @enderror">
        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', ($voucher->is_active ?? true)) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Kích hoạt voucher</label>
        </div>
    </div>

    @if($errors->any())
        <div class="col-12">
            <div class="alert alert-danger">
                <h6 class="alert-heading mb-2">Vui lòng kiểm tra lại các trường sau:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light">
                <i class="ri-close-line me-1"></i> Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line me-1"></i> Lưu thay đổi
            </button>
        </div>
    </div>
</div>


