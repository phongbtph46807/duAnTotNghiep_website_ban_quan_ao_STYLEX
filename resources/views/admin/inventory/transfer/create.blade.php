@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-transfer"></i> Tạo Yêu Cầu Chuyển Kho</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.transfer.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.inventory.transfer.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kho Nguồn <span class="text-danger">*</span></label>
                        <select name="from_warehouse_id" id="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror">
                            <option value="">-- Chọn Kho Nguồn --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kho Đích <span class="text-danger">*</span></label>
                        <select name="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror">
                            <option value="">-- Chọn Kho Đích --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sản Phẩm <span class="text-danger">*</span></label>
                        <select name="variant_id" id="variant_id" class="form-select @error('variant_id') is-invalid @enderror">
                            <option value="">-- Chọn Sản Phẩm --</option>
                            @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}" {{ old('variant_id') == $variant->id ? 'selected' : '' }}>
                                    {{ $variant->product->name }}
                                    @if($variant->color) - {{ $variant->color->name }} @endif
                                    @if($variant->size) - {{ $variant->size->name }} @endif
                                    ({{ $variant->sku }})
                                </option>
                            @endforeach
                        </select>
                        @error('variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="stock-info" class="text-muted d-block mt-2"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số Lượng <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity') }}" min="1">
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lô Hàng <span class="text-danger">*</span></label>
                        <select name="batch_number" id="batch_number" class="form-select @error('batch_number') is-invalid @enderror" required>
                            <option value="">-- Chọn Lô Hàng --</option>
                        </select>
                        @error('batch_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="batch-info" class="text-muted d-block mt-2"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vị Trí Kho</label>
                        <input type="text" id="location" class="form-control" disabled>
                        <input type="hidden" id="location_hidden" name="location" value="">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Người Tạo</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                        <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi Chú</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Tạo Yêu Cầu
                    </button>
                    <a href="{{ route('admin.inventory.transfer.index') }}" class="btn btn-secondary">
                        <i class="bx bx-x"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateBatches() {
    const fromWarehouseId = document.getElementById('from_warehouse_id').value;
    const variantId = document.getElementById('variant_id').value;
    const batchSelect = document.getElementById('batch_number');
    const batchInfo = document.getElementById('batch-info');
    const locationInput = document.getElementById('location');

    batchSelect.innerHTML = '<option value="">-- Chọn Lô Hàng --</option>';
    locationInput.value = '';
    batchInfo.textContent = '';

    if (!fromWarehouseId || !variantId) return;

    fetch(`/admin/inventory/transfer/batches/${fromWarehouseId}/${variantId}`)
        .then(response => response.json())
        .then(batches => {
            console.log('Batches received:', batches);
            if (!batches || batches.length === 0) {
                batchInfo.textContent = 'Không có lô hàng nào có sẵn';
                return;
            }
            batches.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.batch_number;
                option.textContent = `${batch.batch_number} (Sẵn: ${batch.available})`;
                option.dataset.available = batch.available;
                option.dataset.location = batch.location || 'Chưa xác định';
                batchSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            batchInfo.textContent = 'Lỗi tải dữ liệu lô hàng';
        });
}

document.getElementById('from_warehouse_id').addEventListener('change', updateBatches);
document.getElementById('variant_id').addEventListener('change', updateBatches);

document.getElementById('batch_number').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const locationInput = document.getElementById('location');
    const locationHidden = document.getElementById('location_hidden');
    const batchInfo = document.getElementById('batch-info');
    
    if (selectedOption.value) {
        const location = selectedOption.dataset.location || 'Chưa xác định';
        locationInput.value = location;
        locationHidden.value = location;
        batchInfo.textContent = `Có sẵn: ${selectedOption.dataset.available} cái`;
    } else {
        locationInput.value = '';
        locationHidden.value = '';
        batchInfo.textContent = '';
    }
});
</script>
@endsection
