@extends('admin.layouts.app')

@section('title')
    Danh sách kho hàng
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý kho hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sách kho hàng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách kho hàng</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div>

                {{-- Form lọc --}}
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="{{ route('admin.inventory.warehouses.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tìm kiếm</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Nhập mã hoặc tên kho">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Loại kho</label>
                                <select name="type" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="PHYSICAL" {{ request('type') == 'PHYSICAL' ? 'selected' : '' }}>Kho vật lý</option>
                                    <option value="VIRTUAL" {{ request('type') == 'VIRTUAL' ? 'selected' : '' }}>Kho ảo</option>
                                    <option value="CONSIGNMENT" {{ request('type') == 'CONSIGNMENT' ? 'selected' : '' }}>Kho ký gửi</option>
                                    <option value="SCRAP" {{ request('type') == 'SCRAP' ? 'selected' : '' }}>Kho phế liệu</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Đang hoạt động</option>
                                    <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Tạm ngưng</option>
                                    <option value="MAINTENANCE" {{ request('status') == 'MAINTENANCE' ? 'selected' : '' }}>Bảo trì</option>
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="{{ route('admin.inventory.warehouses.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row g-4 mb-3">
                        <div class="col-sm-auto">
                            <div>
                                <a href="{{ route('admin.inventory.warehouses.create') }}" class="btn btn-success add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                                </a>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="d-flex justify-content-sm-end">
                                <div class="search-box ms-2">
                                    <input type="text" name="search_full" id="searchFull"
                                        class="form-control search" placeholder="Tìm kiếm..." 
                                        value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã Kho</th>
                                    <th>Tên Kho</th>
                                    <th>Loại</th>
                                    <th>Trạng Thái</th>
                                    <th>Địa Chỉ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $index => $warehouse)
                                    <tr>
                                        <td>{{ $warehouses->firstItem() + $index }}</td>
                                        <td><span class="badge bg-secondary">{{ $warehouse->code }}</span></td>
                                        <td><strong>{{ $warehouse->name }}</strong></td>
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
                                        <td>{{ Str::limit($warehouse->address ?? '-', 40) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <div class="show">
                                                    <form action="{{ route('admin.inventory.warehouses.show', $warehouse) }}" method="get">
                                                        @csrf
                                                        <button class="btn btn-sm btn-info show-item-btn">
                                                            <i class="las la-eye"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="edit">
                                                    <form action="{{ route('admin.inventory.warehouses.edit', $warehouse) }}" method="get">
                                                        @csrf
                                                        <button class="btn btn-sm btn-warning edit-item-btn">
                                                            <span class="ri-edit-box-line"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="remove">
                                                    <form method="POST" action="{{ route('admin.inventory.warehouses.destroy', $warehouse) }}" class="d-inline delete-form" id="delete-form-{{ $warehouse->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-warehouse-id="{{ $warehouse->id }}" data-warehouse-name="{{ $warehouse->name }}">
                                                            <span class="ri-delete-bin-7-line"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <p class="mb-0">Không tìm thấy kho hàng nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Xin lỗi! Không tìm thấy kết quả</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            @if ($warehouses->onFirstPage())
                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                            @else
                                <a class="page-item pagination-prev" href="{{ $warehouses->previousPageUrl() }}">Previous</a>
                            @endif

                            <ul class="pagination listjs-pagination mb-0">
                                @foreach ($warehouses->getUrlRange(1, $warehouses->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $warehouses->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($warehouses->hasMorePages())
                                <a class="page-item pagination-next" href="{{ $warehouses->nextPageUrl() }}">Next</a>
                            @else
                                <a class="page-item pagination-next disabled" href="javascript:void(0);">Next</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle filter
        $('#toggleFilterBtn').on('click', function() {
            $('#filterForm').slideToggle(200);
        });

        // Xử lý xóa kho
        $('.remove-item-btn').on('click', function(e) {
            e.preventDefault();
            var warehouseId = $(this).data('warehouse-id');
            var warehouseName = $(this).data('warehouse-name');
            
            if (confirm('Bạn có chắc chắn muốn xóa kho "' + warehouseName + '"?\n\n' +
                        'Lưu ý:\n' +
                        '- Không thể xóa kho đang có tồn kho!\n' +
                        '- Dữ liệu sẽ bị xóa vĩnh viễn và không thể khôi phục!')) {
                $('#delete-form-' + warehouseId).submit();
            }
        });
    });
</script>
@endpush
