@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-transfer"></i> Chuyển Kho</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.inventory.transfer.create') }}" class="btn btn-success btn-sm">
                <i class="bx bx-plus"></i> Tạo Yêu Cầu Chuyển
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
                <strong>Quy trình Chuyển Kho:</strong>
                <ol class="mb-0 mt-2 ps-3 small">
                    <li>Tạo yêu cầu chuyển: Chọn kho nguồn, kho đích, sản phẩm, số lượng</li>
                    <li>Xác nhận xuất: Click "Xuất" khi ở trạng thái "Chờ Xuất" - hàng trừ khỏi kho nguồn</li>
                    <li>Xác nhận nhập: Click "Nhập" khi ở trạng thái "Đã Xuất" - hàng vào QUARANTINE kho đích</li>
                    <li>Kiểm tra QC: Click "QC" khi ở trạng thái "Chờ QC" - nhập kết quả QC (pass/fail)</li>
                </ol>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Danh Sách Yêu Cầu Chuyển Kho ({{ $movements->total() }})</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sản Phẩm</th>
                            <th>Lô Hàng</th>
                            <th>Vị Trí</th>
                            <th>Từ Kho</th>
                            <th>Đến Kho</th>
                            <th class="text-end">Số Lượng</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $request)
                            <tr>
                                <td><span class="badge bg-secondary">TR-{{ $request->id }}</span></td>
                                <td>
                                    <div class="small">
                                        <strong>{{ $request->variant->product->name ?? 'N/A' }}</strong><br>
                                        <span class="text-muted">SKU: {{ $request->variant->sku ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $request->batch_number }}</span></td>
                                <td>
                                    @php
                                        $stock = \App\Models\WarehouseStock::where('warehouse_id', $request->from_warehouse_id)
                                            ->where('variant_id', $request->variant_id)
                                            ->where('batch_number', $request->batch_number)
                                            ->first();
                                    @endphp
                                    {{ $stock?->location ?? 'Chưa xác định' }}
                                </td>
                                <td>{{ $request->fromWarehouse->name }}</td>
                                <td>{{ $request->toWarehouse->name }}</td>
                                <td class="text-end">{{ number_format($request->quantity) }}</td>
                                <td>
                                    @if ($request->status === 'PENDING')
                                        <span class="badge bg-warning">Chờ Xuất</span>
                                    @elseif ($request->status === 'OUT_CONFIRMED')
                                        <span class="badge bg-info">Đã Xuất</span>
                                    @elseif ($request->status === 'QC_CHECKING')
                                        <span class="badge bg-primary">Chờ QC</span>
                                    @elseif ($request->status === 'COMPLETED')
                                        <span class="badge bg-success">Hoàn Thành</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    @if ($request->status === 'PENDING')
                                        <form action="{{ route('admin.inventory.transfer.confirm-out', $request->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info" title="Xuất Kho" onclick="return confirm('Xác nhận xuất kho?')">
                                                <i class="bx bx-upload"></i> Xuất
                                            </button>
                                        </form>
                                    @elseif ($request->status === 'OUT_CONFIRMED')
                                        <form action="{{ route('admin.inventory.transfer.confirm-in', $request->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" title="Nhập Kho" onclick="return confirm('Xác nhận nhập kho?')">
                                                <i class="bx bx-download"></i> Nhập
                                            </button>
                                        </form>
                                    @elseif ($request->status === 'QC_CHECKING')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#qcModal{{ $request->id }}" title="Kiểm Tra QC">
                                            <i class="bx bx-check"></i> QC
                                        </button>
                                        <!-- QC Modal -->
                                        <div class="modal fade" id="qcModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Kiểm Tra QC - {{ $request->variant->product->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.inventory.transfer.confirm-qc', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p class="mb-3"><strong>Tổng số lượng:</strong> {{ number_format($request->quantity) }}</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Số Lượng QC Pass <span class="text-danger">*</span></label>
                                                                <input type="number" name="qc_passed_qty" class="form-control" min="0" max="{{ $request->quantity }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Số Lượng QC Fail <span class="text-danger">*</span></label>
                                                                <input type="number" name="qc_failed_qty" class="form-control" min="0" max="{{ $request->quantity }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-success">Xác Nhận QC</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
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
        {{ $movements->links() }}
    </div>
</div>
@endsection
