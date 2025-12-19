@extends('admin.layouts.app')

@section('title')
    Chi tiết kho hàng
@endsection

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chi tiết kho hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.inventory.warehouses.index') }}">Kho hàng</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN 1: THÔNG TIN KHO --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Thông tin kho hàng</h5>
                    <div>
                        <a href="{{ route('admin.inventory.warehouses.edit', $warehouse) }}" class="btn btn-warning btn-sm">
                            <i class="ri-edit-box-line me-1"></i> Sửa
                        </a>
                        <a href="{{ route('admin.inventory.warehouses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold" width="150">Mã kho:</td>
                                        <td><span class="badge bg-secondary fs-6">{{ $warehouse->code }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Tên kho:</td>
                                        <td><strong>{{ $warehouse->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Loại kho:</td>
                                        <td>
                                            @switch($warehouse->type)
                                                @case('PHYSICAL')
                                                    <span class="badge bg-primary-subtle text-primary">Kho vật lý</span>
                                                    @break
                                                @case('VIRTUAL')
                                                    <span class="badge bg-info-subtle text-info">Kho ảo</span>
                                                    @break
                                                @case('CONSIGNMENT')
                                                    <span class="badge bg-warning-subtle text-warning">Kho ký gửi</span>
                                                    @break
                                                @case('SCRAP')
                                                    <span class="badge bg-dark-subtle text-dark">Kho phế liệu</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold" width="150">Trạng thái:</td>
                                        <td>
                                            @switch($warehouse->operational_status)
                                                @case('ACTIVE')
                                                    <span class="badge bg-success-subtle text-success">Đang hoạt động</span>
                                                    @break
                                                @case('INACTIVE')
                                                    <span class="badge bg-danger-subtle text-danger">Tạm ngưng</span>
                                                    @break
                                                @case('MAINTENANCE')
                                                    <span class="badge bg-warning-subtle text-warning">Bảo trì</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Địa chỉ:</td>
                                        <td>{{ $warehouse->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Ngày tạo:</td>
                                        <td>{{ $warehouse->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN 2: TỒN KHO THEO SẢN PHẨM --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Tồn kho theo sản phẩm</h5>
                    <a href="{{ route('admin.inventory.current-stock') }}?warehouse_id={{ $warehouse->id }}" class="btn btn-sm btn-primary">
                        <i class="ri-external-link-line me-1"></i> Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="ri-information-line me-2"></i>
                        Tổng: <strong>{{ $totalProducts }}</strong> sản phẩm | Tồn kho: <strong>{{ number_format($totalStock) }}</strong> sản phẩm
                    </div>

                    @if($stocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Tồn thực tế</th>
                                        <th class="text-center">Sẵn sàng bán</th>
                                        <th class="text-center">Đã đặt</th>
                                        <th class="text-center">Chờ QC</th>
                                        <th class="text-center">Hỏng</th>
                                        <th class="text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $stock)
                                        <tr>
                                            <td>
                                                <strong>{{ $stock->variant->product->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">Biến thể: {{ $stock->variant->sku ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-center"><span class="badge bg-primary">{{ $stock->on_hand }}</span></td>
                                            <td class="text-center"><span class="badge bg-success">{{ $stock->available }}</span></td>
                                            <td class="text-center"><span class="badge bg-warning">{{ $stock->reserved }}</span></td>
                                            <td class="text-center"><span class="badge bg-info">{{ $stock->quarantine }}</span></td>
                                            <td class="text-center"><span class="badge bg-danger">{{ $stock->damaged }}</span></td>
                                            <td class="text-center">
                                                @if($stock->status === 'CONFIRMED')
                                                    <span class="badge bg-success-subtle text-success">Xác nhận</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Chờ</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-inbox-line" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Chưa có tồn kho nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN 3: LỊCH SỪA GIAO DỊCCH GẦN ĐÂY --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Hoạt động gần đây</h5>
                    <a href="{{ route('admin.inventory.logs') }}?warehouse_id={{ $warehouse->id }}" class="btn btn-sm btn-primary">
                        <i class="ri-external-link-line me-1"></i> Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($recentLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Loại giao dịch</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th>Người thực hiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLogs as $log)
                                        <tr>
                                            <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                                            <td>
                                                @switch($log->transaction_type)
                                                    @case('STOCK_IN')
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="ri-download-2-line me-1"></i> Nhập kho
                                                        </span>
                                                        @break
                                                    @case('STOCK_OUT')
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            <i class="ri-upload-2-line me-1"></i> Xuất kho
                                                        </span>
                                                        @break
                                                    @case('TRANSFER_OUT')
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            <i class="ri-arrow-right-line me-1"></i> Chuyển đi
                                                        </span>
                                                        @break
                                                    @case('TRANSFER_IN')
                                                        <span class="badge bg-info-subtle text-info">
                                                            <i class="ri-arrow-left-line me-1"></i> Chuyển đến
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $log->transaction_type }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong>{{ $log->variant->product->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $log->variant->sku ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}</span>
                                            </td>
                                            <td>{{ $log->user->name ?? 'Hệ thống' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-history-line" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Chưa có giao dịch nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
