@extends('admin.layouts.app')
@section('title', 'Quản lý đơn hàng')

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
    </style>
    <style>
        #filterForm {
            position: relative;
            z-index: 10;
            background-color: #fff;
            /* tránh trong suốt */
            border-top: 1px solid #dee2e6;
            margin-top: 5px;
            padding: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý đơn hàng</h4>
            </div>
        </div>
    </div>

    {{-- 🧮 Thống kê --}}
    <div class="row cursor-pointer">
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-file-list-line text-primary stat-icon"></i>
                    <h6 class="text-muted">Tổng đơn hàng</h6>
                    <h3>{{ $orderStats->total_orders ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-time-line text-warning stat-icon"></i>
                    <h6 class="text-muted">Đang xử lý</h6>
                    <h3>{{ $orderStats->processing_orders ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-checkbox-circle-line text-success stat-icon"></i>
                    <h6 class="text-muted">Hoàn tất</h6>
                    <h3>{{ $orderStats->completed_orders ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <i class="ri-close-circle-line text-danger stat-icon"></i>
                    <h6 class="text-muted">Đã hủy</h6>
                    <h3>{{ $orderStats->cancelled_orders ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔎 Bộ lọc --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Danh sách đơn hàng</h4>
            <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                <i class="ri-filter-3-line"></i> Bộ lọc
            </button>
        </div>

        {{-- Form lọc --}}
        <div class="card-body" id="filterForm" style="display: none;">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <div class="row g-3">
                    {{-- Mã đơn hàng --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Mã đơn hàng</label>
                        <input type="text" name="code" value="{{ request('code') }}" class="form-control"
                            placeholder="Nhập mã đơn hàng...">
                    </div>

                    {{-- Tên khách hàng --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tên khách hàng</label>
                        <input type="text" name="full_name" value="{{ request('full_name') }}" class="form-control"
                            placeholder="Nhập tên khách hàng...">
                    </div>

                    {{-- Trạng thái --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái đơn hàng</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                            </option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                            </option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền
                            </option>
                        </select>
                    </div>

                    {{-- Nút lọc và reset --}}
                    <div class="col-md-12 d-flex justify-content-end gap-2 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ri-search-line"></i> Lọc
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-refresh-line"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>


        {{-- 📋 Danh sách --}}
        <div class="card-body">
            <div class="listjs-table" id="orderList">
                <div class="table-responsive table-card mt-3 mb-1">
                    <table class="table align-middle text-center table-nowrap" id="orderTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn</th>
                                <th>Tên KH</th>
                                <th>Email</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->code }}</td>
                                    <td>{{ $order->full_name }}</td>
                                    <td>{{ $order->email }}</td>
                                    <td>{{ number_format($order->total) }}₫</td>
                                    <td>
                                        <span
                                            class="badge {{ $order->payment_status == 'paid' ? 'bg-success-subtle text-success' : ($order->payment_status == 'unpaid' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary') }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm"
                                            onchange="updateStatus({{ $order->id }}, this.value)">
                                            @foreach (['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn tất', 'cancelled' => 'Đã hủy'] as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ $order->status == $key ? 'selected' : '' }}>{{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(orderId, status) {
            const url = "{{ route('admin.orders.updateStatus', ':id') }}".replace(':id', orderId);
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        status
                    })
                })
                .then(res => res.json())
                .then(data => toastr.success(data.message))
                .catch(err => {
                    toastr.error('Lỗi khi cập nhật trạng thái đơn hàng!');
                    console.error(err);
                });
        }

        
    </script>
    <script>
        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterForm').slideToggle(200);
            });
        });
    </script>
@endpush
