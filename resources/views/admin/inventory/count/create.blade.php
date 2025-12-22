@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-list-check"></i> Tạo Yêu Cầu Kiểm Kê</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.count.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.inventory.count.store') }}" method="POST">
                @csrf

                <div class="alert alert-info">
                    <strong>Hướng dẫn:</strong> Chọn Lô Hàng để Kiểm Kê
                </div>

                <div class="mb-3">
                    <label class="form-label">Lô Hàng <span class="text-danger">*</span></label>
                    <select name="batch_number" id="batch_number" class="form-select @error('batch_number') is-invalid @enderror" required>
                        <option value="">-- Chọn Lô Hàng --</option>
                        @forelse ($batches as $batch)
                            <option value="{{ $batch->batch_number }}" 
                                data-warehouse-id="{{ $batch->warehouse_id }}"
                                data-variant-id="{{ $batch->variant_id }}"
                                data-batch-number="{{ $batch->batch_number }}"
                                data-warehouse="{{ $batch->warehouse->name ?? 'N/A' }}"
                                data-product="{{ ($batch->variant && $batch->variant->product ? $batch->variant->product->name : 'N/A') . ($batch->variant && $batch->variant->color ? ' (' . $batch->variant->color->name . ')' : '') . ($batch->variant && $batch->variant->size ? ' (' . $batch->variant->size->name . ')' : '') }}"
                                data-quantity="{{ $batch->on_hand ?? 0 }}"
                                data-location="{{ ($batch->stockInRequest && $batch->stockInRequest->location) ? $batch->stockInRequest->location : ($batch->location ?? 'N/A') }}">
                                {{ $batch->batch_number }} - {{ ($batch->variant && $batch->variant->product ? $batch->variant->product->name : 'N/A') }} - {{ $batch->warehouse->name ?? 'N/A' }} - SL: {{ $batch->on_hand ?? 0 }}
                            </option>
                        @empty
                            <option value="" disabled>Không có lô hàng nào để kiểm kê</option>
                        @endforelse
                    </select>
                    @error('batch_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <input type="hidden" name="warehouse_id" id="warehouse_id">
                <input type="hidden" name="variant_id" id="variant_id">

                <div id="batch-info" class="alert alert-light" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Lô Hàng:</strong> <span id="info-batch-number" class="badge bg-secondary">-</span></p>
                            <p><strong>Kho:</strong> <span id="info-warehouse">-</span></p>
                            <p><strong>Sản Phẩm:</strong> <span id="info-product">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Số Lượng Tồn:</strong> <span id="info-quantity" class="badge bg-info">-</span></p>
                            <p><strong>Vị Trí:</strong> <span id="info-location">-</span></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Tạo Yêu Cầu Kiểm Kê
                    </button>
                    <a href="{{ route('admin.inventory.count.index') }}" class="btn btn-secondary">
                        <i class="bx bx-x"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('batch_number').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('batch-info');
    
    if (this.value && selectedOption) {
        document.getElementById('warehouse_id').value = selectedOption.dataset.warehouseId || '';
        document.getElementById('variant_id').value = selectedOption.dataset.variantId || '';
        document.getElementById('info-batch-number').textContent = selectedOption.dataset.batchNumber || 'N/A';
        document.getElementById('info-warehouse').textContent = selectedOption.dataset.warehouse || 'N/A';
        document.getElementById('info-product').textContent = selectedOption.dataset.product || 'N/A';
        document.getElementById('info-quantity').textContent = selectedOption.dataset.quantity || '0';
        document.getElementById('info-location').textContent = selectedOption.dataset.location || 'N/A';
        infoDiv.style.display = 'block';
    } else {
        document.getElementById('warehouse_id').value = '';
        document.getElementById('variant_id').value = '';
        infoDiv.style.display = 'none';
    }
});
</script>
@endsection
