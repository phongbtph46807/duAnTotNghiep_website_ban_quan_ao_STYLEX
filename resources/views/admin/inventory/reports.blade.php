@extends('admin.layouts.app')
@section('title', 'Báo cáo Hàng hỏng')

@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-3">Báo cáo & Thống kê Hàng hỏng</h2>

    <form action="{{ route('admin.inventory.reports') }}" method="GET" class="card p-2 mb-3">
        <div class="row g-2">
            <div class="col-md-2">
                <select name="time_range" class="form-select form-select-sm">
                    <option value="7" {{ $timeRange == 7 ? 'selected' : '' }}>7 ngày</option>
                    <option value="30" {{ $timeRange == 30 ? 'selected' : '' }}>30 ngày</option>
                    <option value="90" {{ $timeRange == 90 ? 'selected' : '' }}>90 ngày</option>
                    <option value="365" {{ $timeRange == 365 ? 'selected' : '' }}>1 năm</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bx bx-search"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0"><i class="bx bx-error-circle"></i> Thống kê theo Phân loại</h6>
                </div>
                <div class="card-body p-2">
                    @if ($defectStats->isEmpty())
                        <div class="alert alert-info mb-0 small">Không có dữ liệu.</div>
                    @else
                        <ul class="list-group list-group-flush small">
                            @foreach ($defectStats as $classification => $stat)
                                <li class="list-group-item d-flex justify-content-between py-1">
                                    <span>
                                        @if ($classification === 'REWORK')
                                            <span class="badge bg-primary">Sửa chữa</span>
                                        @elseif ($classification === 'B-GRADE')
                                            <span class="badge bg-secondary">Hàng Loại B</span>
                                        @elseif ($classification === 'SCRAP')
                                            <span class="badge bg-dark">Tiêu hủy</span>
                                        @endif
                                    </span>
                                    <span>
                                        <span class="badge bg-light text-dark">{{ $stat->count }} báo cáo</span>
                                        <span class="badge bg-warning">{{ number_format($stat->total_qty) }} cái</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0"><i class="bx bx-alert-triangle"></i> Thống kê theo Mức độ</h6>
                </div>
                <div class="card-body p-2">
                    @if ($defectByLevel->isEmpty())
                        <div class="alert alert-info mb-0 small">Không có dữ liệu.</div>
                    @else
                        <ul class="list-group list-group-flush small">
                            @foreach ($defectByLevel as $level => $stat)
                                <li class="list-group-item d-flex justify-content-between py-1">
                                    <span>
                                        @if ($level === 'LIGHT')
                                            <span class="badge bg-info">Nhẹ</span>
                                        @elseif ($level === 'MEDIUM')
                                            <span class="badge bg-warning">Trung bình</span>
                                        @elseif ($level === 'HEAVY')
                                            <span class="badge bg-danger">Nặng</span>
                                        @endif
                                    </span>
                                    <span>
                                        <span class="badge bg-light text-dark">{{ $stat->count }} báo cáo</span>
                                        <span class="badge bg-danger">{{ number_format($stat->total_qty) }} cái</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-body { font-size: 0.875rem; }
    .list-group-item { padding: 0.4rem 0.75rem !important; }
</style>
@endsection
