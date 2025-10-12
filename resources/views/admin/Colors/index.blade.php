@extends('admin.layouts.app')
@section('title', 'Quản lí màu sắc')
@push('page-css')
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

<style>
    .stat-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 150px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .color-table th,
    .color-table td {
        vertical-align: middle;
    }

    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #ddd;
        display: inline-block;
    }
</style>
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lí màu sắc</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">Thuộc tính sản phẩm</a></li>
                    <li class="breadcrumb-item">Quản lí màu sắc</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<!-- Thống kê màu sắc -->
<div class="row cursor-pointer">
    <div class="col-12 col-sm-6 col-md-3 mb-3">
        <div class="card stats-card total-card">
            <div class="card-body text-center">
                <div class="stat-icon text-primary">
                    <i class="ri-palette-line"></i>
                </div>
                <h5 class="card-title text-muted mb-2">Tổng số màu</h5>
                <h3 class="card-text fw-bold">{{ $colors->total() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 mb-3">
        <div class="card stats-card approved-card">
            <div class="card-body text-center">
                <div class="stat-icon text-success">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <h5 class="card-title text-muted mb-2">Màu hoạt động</h5>
                <h3 class="card-text fw-bold text-success">{{ $colors->where('status', 1)->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 mb-3">
        <div class="card stats-card pending-card">
            <div class="card-body text-center">
                <div class="stat-icon text-warning">
                    <i class="ri-pause-circle-line"></i>
                </div>
                <h5 class="card-title text-muted mb-2">Màu không hoạt động</h5>
                <h3 class="card-text fw-bold text-warning">{{ $colors->where('status', 0)->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 mb-3">
        <div class="card stats-card parent-card">
            <div class="card-body text-center">
                <div class="stat-icon text-info">
                    <i class="ri-palette-line"></i>
                </div>
                <h5 class="card-title text-muted mb-2">Màu có preview</h5>
                <h3 class="card-text fw-bold text-info">{{ $colors->whereNotNull('hex_code')->count() ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách màu sắc</h4>
                <a href="{{ route('admin.colors.create') }}" class="btn btn-success add-btn">
                    <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg color-table">
                        <thead>
                            <tr>
                                <th>Màu sắc</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($colors as $color)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($color->hex_code)
                                        <div class="color-preview me-2" style="background-color: {{ $color->hex_code }}"></div>
                                        @endif
                                        <span>{{ $color->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($color->status == 1)
                                    <span class="badge bg-success">Hoạt động</span>
                                    @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.colors.edit', $color) }}" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.colors.destroy', $color) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa màu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Chưa có màu sắc nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($colors->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $colors->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection