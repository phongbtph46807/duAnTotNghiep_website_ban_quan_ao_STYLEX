@extends('admin.layouts.app')

@section('title', 'Đánh Giá Hàng Hỏng')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bi bi-check"></i> Đánh Giá Hàng Hỏng - {{ $defect->variant->product->name }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.defect.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thông Tin Báo Cáo</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Sản Phẩm:</strong> {{ $defect->variant->product->name }}</p>
                    <p><strong>SKU:</strong> {{ $defect->variant->sku }}</p>
                    <p><strong>Kho:</strong> {{ $defect->warehouse->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Số Lượng Hỏng:</strong> <span class="badge bg-warning">{{ number_format($defect->quantity) }}</span></p>
                    <p><strong>Mức Độ:</strong> 
                        <span class="badge bg-{{ $defect->defect_level === 'LIGHT' ? 'info' : ($defect->defect_level === 'MEDIUM' ? 'warning' : 'danger') }}">
                            {{ $defect->defect_level }}
                        </span>
                    </p>
                    <p><strong>Trạng Thái:</strong> <span class="badge bg-primary">{{ $defect->status }}</span></p>
                </div>
            </div>
            @if ($defect->description)
                <div class="mt-3">
                    <p><strong>Mô Tả Chi Tiết:</strong></p>
                    <p class="text-muted">{{ $defect->description }}</p>
                </div>
            @endif
            <div class="mt-3">
                <p><strong>Người Báo Cáo:</strong> {{ $defect->createdBy->name ?? 'N/A' }} - {{ $defect->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if ($defect->status === 'PENDING')
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Đánh Giá Hàng Hỏng</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Hướng dẫn:</strong> Nhập loại lỗi, mô tả chi tiết và phân loại xử lý hàng hỏng
            </div>

            <form action="{{ route('admin.inventory.defect.approve', $defect->id) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Loại Lỗi <span class="text-danger">*</span></label>
                        <input type="text" name="defect_type" class="form-control @error('defect_type') is-invalid @enderror" 
                               placeholder="VD: Nước, Xước, Lỗi may..." value="{{ old('defect_type', $defect->defect_type) }}" required>
                        @error('defect_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phân Loại <span class="text-danger">*</span></label>
                        <select name="classification" class="form-select @error('classification') is-invalid @enderror" required>
                            <option value="">-- Chọn phân loại --</option>
                            <option value="REWORK" {{ old('classification', $defect->classification) === 'REWORK' ? 'selected' : '' }}>Sửa Chữa</option>
                            <option value="B-GRADE" {{ old('classification', $defect->classification) === 'B-GRADE' ? 'selected' : '' }}>Hàng Loại B</option>
                            <option value="SCRAP" {{ old('classification', $defect->classification) === 'SCRAP' ? 'selected' : '' }}>Thanh Lý</option>
                        </select>
                        @error('classification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô Tả Lỗi <span class="text-danger">*</span></label>
                    <textarea name="defect_description" class="form-control @error('defect_description') is-invalid @enderror" 
                              rows="3" placeholder="Mô tả chi tiết lỗi..." required>{{ old('defect_description', $defect->defect_description) }}</textarea>
                    @error('defect_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chi Phí Sửa Chữa (VNĐ)</label>
                        <input type="number" name="repair_cost" class="form-control @error('repair_cost') is-invalid @enderror" 
                               step="1" min="0" value="{{ old('repair_cost', $defect->repair_cost ?? 0) }}" placeholder="0">
                        @error('repair_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ghi Chú</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="2" placeholder="Ghi chú thêm...">{{ old('notes', $defect->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check"></i> Phê Duyệt
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Từ Chối
                    </button>
                    <a href="{{ route('admin.inventory.defect.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay Lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    @elseif ($defect->status === 'APPROVED')
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Hoàn Thành Xử Lý</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Hướng dẫn:</strong> Nhập chi phí sửa chữa, vật tư và chi phí phát sinh để hoàn thành xử lý
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Loại Lỗi:</strong> {{ $defect->defect_type }}</p>
                    <p><strong>Phân Loại:</strong>
                        @if ($defect->classification === 'REWORK')
                            <span class="badge bg-primary">Sửa Chữa</span>
                        @elseif ($defect->classification === 'B-GRADE')
                            <span class="badge bg-secondary">Hàng Loại B</span>
                        @elseif ($defect->classification === 'SCRAP')
                            <span class="badge bg-dark">Thanh Lý</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Người Phê Duyệt:</strong> {{ $defect->approvedBy->name ?? 'N/A' }}</p>
                    <p><strong>Thời Gian:</strong> {{ $defect->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <form action="{{ route('admin.inventory.defect.complete', $defect->id) }}" method="POST">
                @csrf
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check"></i> Hoàn Thành
                    </button>
                    <a href="{{ route('admin.inventory.defect.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay Lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    @elseif ($defect->status === 'COMPLETED')
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Đã Hoàn Thành</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Loại Lỗi:</strong> {{ $defect->defect_type }}</p>
                    <p><strong>Phân Loại:</strong>
                        @if ($defect->classification === 'REWORK')
                            <span class="badge bg-primary">Sửa Chữa</span>
                        @elseif ($defect->classification === 'B-GRADE')
                            <span class="badge bg-secondary">Hàng Loại B</span>
                        @elseif ($defect->classification === 'SCRAP')
                            <span class="badge bg-dark">Thanh Lý</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Người Phê Duyệt:</strong> {{ $defect->approvedBy->name ?? 'N/A' }}</p>
                    <p><strong>Thời Gian:</strong> {{ $defect->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-3">
                <p><strong>Chi Phí Sửa Chữa:</strong> {{ number_format($defect->repair_cost ?? 0) }} VNĐ</p>
            </div>

            <div class="mb-3">
                <p><strong>Ghi Chú:</strong></p>
                <p class="text-muted">{{ $defect->notes ?? 'Không có ghi chú' }}</p>
            </div>

            <a href="{{ route('admin.inventory.defect.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>

    @elseif ($defect->status === 'REJECTED')
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Đã Từ Chối</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Loại Lỗi:</strong> {{ $defect->defect_type }}</p>
                    <p><strong>Phân Loại:</strong>
                        @if ($defect->classification === 'REWORK')
                            <span class="badge bg-primary">Sửa Chữa</span>
                        @elseif ($defect->classification === 'B-GRADE')
                            <span class="badge bg-secondary">Hàng Loại B</span>
                        @elseif ($defect->classification === 'SCRAP')
                            <span class="badge bg-dark">Thanh Lý</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Người Từ Chối:</strong> {{ $defect->rejectedBy->name ?? 'N/A' }}</p>
                    <p><strong>Thời Gian:</strong> {{ $defect->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="alert alert-danger">
                <strong>Lý Do Từ Chối:</strong><br>
                {{ $defect->rejection_reason }}
            </div>

            <a href="{{ route('admin.inventory.defect.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Modal Từ Chối -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ Chối Đánh Giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.inventory.defect.reject', $defect->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý Do Từ Chối <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  rows="4" placeholder="Nhập lý do từ chối..." required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Từ Chối</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
