@extends('admin.layouts.app')
@section('title', 'Danh sách phần thưởng Spin')

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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lí Spin</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Quản lí Spin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.spins.index') }}">Danh sách phần thưởng</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row cursor-pointer">
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card total-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-primary">
                        <i class="ri-gift-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng phần thưởng</h5>
                    <h3 class="card-text fw-bold">{{ $spinCounts->total_spins ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card approved-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Đang hoạt động</h5>
                    <h3 class="card-text fw-bold text-success">{{ $spinCounts->active_spins ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card pending-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-warning">
                        <i class="ri-pause-circle-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Không hoạt động</h5>
                    <h3 class="card-text fw-bold text-warning">{{ $spinCounts->inactive_spins ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card stat-card rejected-card">
                <div class="card-body text-center">
                    <div class="stat-icon text-info">
                        <i class="ri-trophy-line"></i>
                    </div>
                    <h5 class="card-title text-muted mb-2">Tổng lượt quay</h5>
                    <h3 class="card-text fw-bold text-info">{{ $spinCounts->total_spun ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh sách phần thưởng Spin</h4>
                    <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                        <i class="ri-filter-3-line"></i> Bộ lọc
                    </button>
                </div>

                {{-- Form lọc --}}
                <div class="card-body" id="filterForm" style="display: none;">
                    <form action="{{ route('admin.spins.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tên phần thưởng</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Nhập tên">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="{{ route('admin.spins.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row g-4 mb-3">
                        <div class="col-sm-auto">
                            <a href="{{ route('admin.spins.create') }}" class="btn btn-success add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                            </a>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form method="GET" action="{{ route('admin.spins.index') }}" class="d-flex align-items-center" style="max-width: 320px;">
                                <div class="input-group">
                                    <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Tìm kiếm...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle text-center table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên phần thưởng</th>
                                <th>Voucher</th>
                                <th>Xác suất (%)</th>
                                <th>Lượt quay</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if($item->voucher)
                                            <span class="badge bg-primary">{{ $item->voucher->code }}</span>
                                        @else
                                            <span class="badge bg-secondary">Không có</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->probability }}%</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->spinUsers->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch form-switch-success d-inline-block">
                                            <input class="form-check-input" type="checkbox" @checked($item->is_active)
                                            onchange="toggleStatus({{ $item->id }}, this.checked)">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.spins.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
{{--                                            <a href="{{ route('admin.spins.show', $item->id) }}" class="btn btn-sm btn-info">--}}
{{--                                                <i class="las la-eye"></i>--}}
{{--                                            </a>--}}
                                            <form method="POST" action="{{ route('admin.spins.destroy', $item->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-name="{{ $item->name }}">
                                                    <i class="ri-delete-bin-7-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            @if ($items->onFirstPage())
                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">Previous</a>
                            @else
                                <a class="page-item pagination-prev" href="{{ $items->previousPageUrl() }}">Previous</a>
                            @endif

                            <ul class="pagination listjs-pagination mb-0">
                                @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($items->hasMorePages())
                                <a class="page-item pagination-next" href="{{ $items->nextPageUrl() }}">Next</a>
                            @else
                                <a class="page-item pagination-next disabled" href="javascript:void(0);">Next</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleStatus(spinId, isChecked) {
            const url = "{{ route('admin.spins.toggleStatus', ':id') }}".replace(':id', spinId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ is_active: isChecked ? 1 : 0 })
            })
                .then(res => res.json())
                .then(data => toastr.success(data.message))
                .catch(err => toastr.error('Lỗi cập nhật trạng thái!'));
        }

        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
@endpush
