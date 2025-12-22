@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-check"></i> Đếm Kho - {{ $request->variant->product->name }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.count.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thông Tin Sản Phẩm & Kho</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Sản Phẩm:</strong> {{ $request->variant->product->name }}</p>
                    <p><strong>SKU:</strong> {{ $request->variant->sku ?? 'N/A' }}</p>
                    <p><strong>Kho:</strong> {{ $request->warehouse->name }}</p>
                    @if ($request->batch_number)
                        <p><strong>Lô Hàng:</strong> <span class="badge bg-secondary">{{ $request->batch_number }}</span></p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày Tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Người Tạo:</strong> {{ $request->createdBy->name ?? 'N/A' }}</p>
                    <p><strong>Trạng Thái:</strong> <span class="badge bg-warning">{{ $request->status }}</span></p>
                    @if ($batchInfo)
                        <p><strong>Vị Trí:</strong> {{ $batchInfo->location ?? 'N/A' }}</p>
                        <p><strong>Giá Nhập:</strong> {{ number_format($batchInfo->cost_price ?? 0, 0) }} đ</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">Tồn Kho Hiện Tại (Hệ Thống)</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <p class="text-muted mb-1">Tồn Kho Tổng</p>
                    <h4 class="text-primary">{{ number_format($stock->on_hand ?? 0) }}</h4>
                </div>
                <div class="col-md-2 text-center">
                    <p class="text-muted mb-1">Sẵn Sàng</p>
                    <h4 class="text-success">{{ number_format($stock->available ?? 0) }}</h4>
                </div>
                <div class="col-md-2 text-center">
                    <p class="text-muted mb-1">Đã Đặt</p>
                    <h4 class="text-info">{{ number_format($stock->reserved ?? 0) }}</h4>
                </div>
                <div class="col-md-2 text-center">
                    <p class="text-muted mb-1">Chờ QC</p>
                    <h4 class="text-warning">{{ number_format($stock->quarantine ?? 0) }}</h4>
                </div>
                <div class="col-md-2 text-center">
                    <p class="text-muted mb-1">Hỏng</p>
                    <h4 class="text-danger">{{ number_format($stock->damaged ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">Chi Tiết Loại Hàng</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Loại Hàng</th>
                            <th class="text-end">Số Lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success">Sẵn Sàng</span></td>
                            <td class="text-end"><strong>{{ number_format($stock->available ?? 0) }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">Đã Đặt</span></td>
                            <td class="text-end"><strong>{{ number_format($stock->reserved ?? 0) }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">Chờ QC</span></td>
                            <td class="text-end"><strong>{{ number_format($stock->quarantine ?? 0) }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">Hỏng</span></td>
                            <td class="text-end"><strong>{{ number_format($stock->damaged ?? 0) }}</strong></td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>Tổng Cộng</strong></td>
                            <td class="text-end"><strong>{{ number_format($stock->on_hand ?? 0) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Nhập Kết Quả Đếm</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory.count.confirm-count', $request->id) }}" method="POST" id="countForm">
                @csrf

                <div class="alert alert-info">
                    <strong>Hướng dẫn:</strong> Nhập số lượng thực tế đếm được theo từng loại. Hệ thống sẽ cập nhật tồn kho ngay sau khi xác nhận.
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số Lượng Sẵn Sàng <span class="text-danger">*</span></label>
                        <input type="number" name="available_qty" id="available_qty" class="form-control @error('available_qty') is-invalid @enderror" 
                               value="{{ old('available_qty', $stock->available ?? 0) }}" min="0" required>
                        @error('available_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số Lượng Đã Đặt <span class="text-danger">*</span></label>
                        <input type="number" name="reserved_qty" id="reserved_qty" class="form-control @error('reserved_qty') is-invalid @enderror" 
                               value="{{ old('reserved_qty', $stock->reserved ?? 0) }}" min="0" required>
                        @error('reserved_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số Lượng Chờ QC <span class="text-danger">*</span></label>
                        <input type="number" name="quarantine_qty" id="quarantine_qty" class="form-control @error('quarantine_qty') is-invalid @enderror" 
                               value="{{ old('quarantine_qty', $stock->quarantine ?? 0) }}" min="0" required>
                        @error('quarantine_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số Lượng Hỏng <span class="text-danger">*</span></label>
                        <input type="number" name="damaged_qty" id="damaged_qty" class="form-control @error('damaged_qty') is-invalid @enderror" 
                               value="{{ old('damaged_qty', $stock->damaged ?? 0) }}" min="0" required>
                        @error('damaged_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Tổng Số Lượng Đếm Được:</strong> <span id="total-display" class="badge bg-primary">{{ $stock->on_hand ?? 0 }}</span>
                    <br><strong>Chênh Lệch:</strong> <span id="difference-display" class="badge bg-warning">0</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi Chú / Lý Do Kiểm Kê</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Ví dụ: Kiểm kê định kỳ, phát hiện mất hàng, ...">{{ old('notes') }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div id="defect-section" style="display: none;" class="mb-3">
                    <div class="alert alert-warning">
                        <strong>Đánh Giá Hàng Hỏng</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mức Độ Hỏng <span class="text-danger">*</span></label>
                            <select name="defect_level" id="defect_level" class="form-select">
                                <option value="">-- Chọn Mức Độ --</option>
                                <option value="LIGHT">Nhẹ (Light)</option>
                                <option value="MEDIUM" selected>Trung Bình (Medium)</option>
                                <option value="HEAVY">Nặng (Heavy)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phân Loại <span class="text-danger">*</span></label>
                            <select name="classification" id="classification" class="form-select">
                                <option value="">-- Chọn Phân Loại --</option>
                                <option value="REWORK">Sửa Chữa (Rework)</option>
                                <option value="SCRAP">Loại Bỏ (Scrap)</option>
                                <option value="B-GRADE">Hạng B (B-Grade)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Xác Nhận Đếm
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
function updateTotal() {
    const available = parseInt(document.getElementById('available_qty').value) || 0;
    const reserved = parseInt(document.getElementById('reserved_qty').value) || 0;
    const quarantine = parseInt(document.getElementById('quarantine_qty').value) || 0;
    const damaged = parseInt(document.getElementById('damaged_qty').value) || 0;
    const total = available + reserved + quarantine + damaged;
    const systemQty = {{ $stock->on_hand ?? 0 }};
    const difference = total - systemQty;
    
    document.getElementById('total-display').textContent = total;
    document.getElementById('difference-display').textContent = (difference >= 0 ? '+' : '') + difference;
    document.getElementById('difference-display').className = difference === 0 ? 'badge bg-success' : (difference > 0 ? 'badge bg-info' : 'badge bg-danger');
    
    const defectSection = document.getElementById('defect-section');
    if (damaged > 0) {
        defectSection.style.display = 'block';
    } else {
        defectSection.style.display = 'none';
    }
}

document.getElementById('available_qty').addEventListener('input', updateTotal);
document.getElementById('reserved_qty').addEventListener('input', updateTotal);
document.getElementById('quarantine_qty').addEventListener('input', updateTotal);
document.getElementById('damaged_qty').addEventListener('input', updateTotal);

updateTotal();
</script>
@endsection
