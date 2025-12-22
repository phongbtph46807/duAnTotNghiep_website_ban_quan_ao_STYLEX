@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-list-check"></i> Kiểm Kê</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.count.create') }}" class="btn btn-success btn-sm">
                <i class="bx bx-plus"></i> Tạo Yêu Cầu Kiểm Kê
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-x-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 mt-1"></i>
            <div>
                <strong>Quy trình Kiểm Kê (Mới):</strong>
                <ol class="mb-0 mt-2 ps-3 small">
                    <li><strong>Tạo yêu cầu:</strong> Chọn kho, sản phẩm, lô hàng (nếu có) (Status: PENDING)</li>
                    <li><strong>Đếm kho:</strong> Click "Đếm Kho" → nhập số lượng thực tế → Hệ thống cập nhật stock ngay (Status: CONFIRMED)</li>
                    <li><strong>Điều chỉnh (nếu cần):</strong> Click "Điều Chỉnh" → sửa lại số lượng nếu phát hiện sai (Status: ADJUSTED)</li>
                </ol>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Danh Sách Yêu Cầu Kiểm Kê ({{ $requests->total() }})</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Lô Hàng</th>
                            <th>Sản Phẩm</th>
                            <th class="text-end">Tồn Hệ Thống</th>
                            <th class="text-end">Tồn Thực Tế</th>
                            <th class="text-end">Chênh Lệch</th>
                            <th class="text-center">Sẵn Sàng</th>
                            <th class="text-center">Đã Đặt</th>
                            <th class="text-center">Chờ QC</th>
                            <th class="text-center">Hỏng</th>
                            <th>Trạng Thái</th>
                            <th>Người Đếm</th>
                            <th class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td>
                                    @if ($request->batch_number)
                                        <span class="badge bg-secondary">{{ $request->batch_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $request->variant->product->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($request->system_qty) }}</td>
                                <td class="text-end">{{ number_format($request->current_stock ?? 0) }}</td>
                                <td class="text-end">
                                    @if ($request->difference !== null)
                                        <span class="badge {{ $request->difference > 0 ? 'bg-success' : ($request->difference < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ $request->difference > 0 ? '+' : '' }}{{ number_format($request->difference) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small>{{ $request->available_qty !== null ? number_format($request->available_qty) : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <small>{{ $request->reserved_qty !== null ? number_format($request->reserved_qty) : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <small>{{ $request->quarantine_qty !== null ? number_format($request->quarantine_qty) : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <small>{{ $request->damaged_qty !== null ? number_format($request->damaged_qty) : '-' }}</small>
                                </td>
                                <td>
                                    @if ($request->status === 'PENDING')
                                        <span class="badge bg-warning">Chờ Đếm</span>
                                    @elseif ($request->status === 'PENDING_ADJUSTMENT')
                                        <span class="badge bg-info">Chờ Điều Chỉnh</span>
                                    @elseif ($request->status === 'CONFIRMED')
                                        <span class="badge bg-success">Hoàn Thành</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $request->countedBy?->name ?? '-' }}</small>
                                </td>
                                <td class="text-end">
                                    @if ($request->status === 'PENDING')
                                        <a href="{{ route('admin.inventory.count.count', $request->id) }}" class="btn btn-sm btn-info" title="Đếm Kho">
                                            <i class="bx bx-check"></i>
                                        </a>
                                    @elseif ($request->status === 'PENDING_ADJUSTMENT')
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#adjustmentModal{{ $request->id }}" title="Điều Chỉnh">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-secondary">Hoàn Thành</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($request->status === 'PENDING_ADJUSTMENT')
                                <div class="modal fade" id="adjustmentModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Điều Chỉnh Kho</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.inventory.count.confirm-adjustment', $request->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p class="text-muted small mb-3">Sản phẩm: <strong>{{ $request->variant->product->name }}</strong></p>
                                                    @if ($request->batch_number)
                                                        <p class="text-muted small mb-3">Lô: <strong>{{ $request->batch_number }}</strong></p>
                                                    @endif
                                                    <div class="mb-3">
                                                        <label class="form-label">Số Lượng Sẵn Sàng <span class="text-danger">*</span></label>
                                                        <input type="number" name="available_qty" class="form-control" value="{{ $request->available_qty ?? 0 }}" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số Lượng Đã Đặt <span class="text-danger">*</span></label>
                                                        <input type="number" name="reserved_qty" class="form-control" value="{{ $request->reserved_qty ?? 0 }}" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số Lượng Chờ QC <span class="text-danger">*</span></label>
                                                        <input type="number" name="quarantine_qty" class="form-control" value="{{ $request->quarantine_qty ?? 0 }}" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số Lượng Hỏng <span class="text-danger">*</span></label>
                                                        <input type="number" name="damaged_qty" id="damaged_qty_{{ $request->id }}" class="form-control damaged-qty-input" value="{{ $request->damaged_qty ?? 0 }}" min="0" required data-modal-id="{{ $request->id }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ghi Chú</label>
                                                        <textarea name="notes" class="form-control" rows="2" placeholder="Lý do điều chỉnh...">{{ $request->notes }}</textarea>
                                                    </div>
                                                    <div id="defect-section-{{ $request->id }}" style="display: none;" class="mb-3">
                                                        <div class="alert alert-warning">
                                                            <strong>Đánh Giá Hàng Hỏng</strong>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Mức Độ Hỏng <span class="text-danger">*</span></label>
                                                                <select name="defect_level" class="form-select">
                                                                    <option value="">-- Chọn Mức Độ --</option>
                                                                    <option value="LIGHT">Nhẹ (Light)</option>
                                                                    <option value="MEDIUM" selected>Trung Bình (Medium)</option>
                                                                    <option value="HEAVY">Nặng (Heavy)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Phân Loại <span class="text-danger">*</span></label>
                                                                <select name="classification" class="form-select">
                                                                    <option value="">-- Chọn Phân Loại --</option>
                                                                    <option value="REWORK">Sửa Chữa (Rework)</option>
                                                                    <option value="SCRAP">Loại Bỏ (Scrap)</option>
                                                                    <option value="B-GRADE">Hạng B (B-Grade)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-warning">Điều Chỉnh</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="bx bx-inbox"></i> Không có yêu cầu nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $requests->links() }}
    </div>
</div>

<script>
document.querySelectorAll('.damaged-qty-input').forEach(input => {
    input.addEventListener('input', function() {
        const modalId = this.dataset.modalId;
        const defectSection = document.getElementById('defect-section-' + modalId);
        if (this.value > 0) {
            defectSection.style.display = 'block';
        } else {
            defectSection.style.display = 'none';
        }
    });
});
</script>
@endsection
