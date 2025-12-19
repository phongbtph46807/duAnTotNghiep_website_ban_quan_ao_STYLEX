@extends('admin.layouts.app')
@section('title', 'Trang quản trị - Style X')
@section('content')
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h4 class="fs-16 mb-0 me-2">Chào mừng, {{ Auth::user()->name }}!</h4>
                                    @if ($userRole === 'admin')
                                        <span class="badge bg-danger">ADMIN</span>
                                    @elseif($userRole === 'staff')
                                        <span class="badge bg-info">STAFF</span>
                                    @elseif($userRole === 'staff')
                                        <span class="badge bg-info">STAFF</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-0">Dashboard tổng quan warehouse hệ thống</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form method="GET">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <select name="period" class="form-select" onchange="this.form.submit()">
                                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 ngày qua
                                                </option>
                                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 ngày qua
                                                </option>
                                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 ngày qua
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('admin.inventory.stock-in.create') }}"
                                                class="btn btn-soft-success">
                                                <i class="ri-add-circle-line align-middle me-1"></i> Nhập kho
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('admin.inventory.reports') }}" class="btn btn-soft-info">
                                                <i class="ri-file-chart-line align-middle me-1"></i> Báo cáo
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng giá trị tồn
                                            kho</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            {{ number_format($totalInventoryValue, 0, ',', '.') }} VNĐ
                                        </h4>
                                        <a href="{{ route('admin.inventory.current-stock') }}"
                                            class="text-decoration-underline">Xem chi tiết</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-package text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Phiếu nhập kho</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stockInCount }}</h4>
                                        <a href="{{ route('admin.inventory.stock-in.index') }}"
                                            class="text-decoration-underline">Xem tất cả</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-import text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tỷ lệ QC Pass</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $qcPassRate }}%</h4>
                                        <span class="text-muted">Chất lượng kiểm tra</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-check-shield text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Hàng sắp hết</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $lowStockCount }}</h4>
                                        <span class="text-muted">Cần bổ sung</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                                            <i class="bx bx-error text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Hoạt động kho</h4>
                            </div>
                            <div class="card-header p-0 border-0 bg-light-subtle">
                                <div class="row g-0 text-center">
                                    <div class="col-4">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1">{{ $stockInCount }}</h5>
                                            <p class="text-muted mb-0">Phiếu nhập</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1">{{ $stockOutCount }}</h5>
                                            <p class="text-muted mb-0">Phiếu xuất</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1">{{ $transferCount }}</h5>
                                            <p class="text-muted mb-0">Chuyển kho</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <p class="text-muted">Biểu đồ hoạt động kho sẽ được hiển thị tại đây</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Top 10 sản phẩm giá trị cao</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Màu/Size</th>
                                                <th>Giá trị</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topProducts as $product)
                                                <tr>
                                                    <td>
                                                        <h5 class="fs-14 my-1">{{ $product->name }}</h5>
                                                    </td>
                                                    <td>
                                                        @if ($product->color)
                                                            <span
                                                                class="badge bg-info-subtle text-info">{{ $product->color }}</span>
                                                        @endif
                                                        @if ($product->size)
                                                            <span
                                                                class="badge bg-secondary-subtle text-secondary">{{ $product->size }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">
                                                            {{ number_format($product->stock_value, 0, ',', '.') }} VNĐ
                                                        </h5>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
