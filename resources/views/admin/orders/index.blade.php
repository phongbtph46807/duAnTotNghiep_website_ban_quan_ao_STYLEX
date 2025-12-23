@extends('admin.layouts.app')
@section('title', 'Quản lý đơn hàng')

@push('page-css')
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .stat-card {
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            padding: 18px 22px;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all .25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px -15px rgba(15, 23, 42, 0.4);
        }
        .stat-card .stat-label {
            font-size: 13px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
        }
        .stat-trend {
            font-size: 13px;
        }
        #filterForm {
            position: relative;
            z-index: 10;
            background-color: #fff;
            border-top: 1px solid #e2e8f0;
            margin-top: 5px;
            padding: 18px;
        }
        .order-table thead th {
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #94a3b8;
            border-bottom: none;
        }
        .order-table tbody td {
            vertical-align: middle;
        }
        .badge-dot {
            position: relative;
            padding-left: 14px;
        }
        .badge-dot::before {
            content: '';
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            left: 4px;
            top: 50%;
            transform: translateY(-50%);
        }
        .badge-dot.bg-pending::before { background: #f59e0b; }
        .badge-dot.bg-processing::before { background: #3b82f6; }
        .badge-dot.bg-completed::before { background: #10b981; }
        .badge-dot.bg-cancelled::before { background: #ef4444; }
        .timeline-step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }
        .timeline-step .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5f5;
        }
        .timeline-step.active .dot {
            background: #2563eb;
            box-shadow: 0 0 0 6px rgba(37, 99, 235, .15);
        }
        .order-detail-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: .08em;
        }
        .order-detail-value {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
        }
        .product-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
        }
        .status-pill {
            border-radius: 20px;
            font-weight: 600;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .status-pill.status-pending { background: #fff7ed; color: #c2410c; }
        .status-pill.status-processing { background: #eff6ff; color: #1d4ed8; }
        .status-pill.status-shipping { background: #e0f2fe; color: #0369a1; }
        .status-pill.status-completed { background: #ecfdf5; color: #059669; }
        .status-pill.status-cancelled { background: #fef2f2; color: #b91c1c; }
        .status-pill.status-returned { background: #fef3c7; color: #92400e; }
        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
        }
        .status-menu {
            min-width: 280px;
            padding: 4px;
        }
        .status-menu .dropdown-item {
            border-radius: 10px;
            padding: 10px 14px;
            gap: 10px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
        }
        .status-dot.status-pending { background: #fb923c; }
        .status-dot.status-processing { background: #3b82f6; }
        .status-dot.status-shipping { background: #0ea5e9; }
        .status-dot.status-completed { background: #22c55e; }
        .status-dot.status-cancelled { background: #ef4444; }
        .status-dot.status-returned { background: #f97316; }
        .status-action.disabled {
            pointer-events: none;
            opacity: 0.5;
        }
        /* Giữ dropdown trạng thái không bị che theo chiều dọc nhưng vẫn cho phép bảng scroll ngang trên màn nhỏ */
        .order-table-wrapper {
            position: relative; /* làm gốc cho dropdown */
            overflow-x: auto !important;
            overflow-y: visible !important;
        }
        /* Dropdown trạng thái hiển thị dạng overlay, nằm trên các dòng khác nhưng không bị cắt */
        .order-table-wrapper .dropdown-menu {
            z-index: 99999 !important; /* rất cao để luôn nổi trên tất cả card */
            position: fixed !important; /* dùng fixed để luôn nổi trên mọi thứ */
        }
        /* Card Danh sách đơn hàng không cắt dropdown và nằm trên card Yêu cầu hủy/trả hàng */
        .orders-list-card {
            position: relative;
            z-index: 100;
        }
        .orders-list-card .card-body {
            overflow: visible;
        }
        /* Card Yêu cầu hủy/trả hàng có z-index thấp hơn để không che dropdown */
        .request-orders-card {
            position: relative;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    @php
        $statusStyles = [
            'pending' => [
                'label' => 'Chờ xác nhận',
                'class' => 'text-warning',
                'icon' => 'ri-time-line',
                'pill' => 'status-pending',
                'desc' => 'Đơn mới, chờ nhân viên xác nhận'
            ],
            'processing' => [
                'label' => 'Đang xử lý',
                'class' => 'text-primary',
                'icon' => 'ri-loader-4-line',
                'pill' => 'status-processing',
                'desc' => 'Đang chuẩn bị & đóng gói tại kho'
            ],
            'shipping' => [
                'label' => 'Chờ giao hàng',
                'class' => 'text-info',
                'icon' => 'ri-truck-line',
                'pill' => 'status-shipping',
                'desc' => 'Đã bàn giao cho đơn vị vận chuyển'
            ],
            'delivered' => [
                'label' => 'Đã giao',
                'class' => 'text-success',
                'icon' => 'ri-checkbox-circle-line',
                'pill' => 'status-completed',
                'desc' => 'Đơn đã giao đến khách'
            ],
            'completed' => [
                'label' => 'Hoàn thành',
                'class' => 'text-success',
                'icon' => 'ri-check-double-line',
                'pill' => 'status-completed',
                'desc' => 'Đơn đã giao thành công'
            ],
            'cancelled' => [
                'label' => 'Đã hủy',
                'class' => 'text-danger',
                'icon' => 'ri-close-line',
                'pill' => 'status-cancelled',
                'desc' => 'Đơn bị hủy theo yêu cầu'
            ],
            'cancel_request' => [
                'label' => 'Yêu cầu hủy',
                'class' => 'text-danger',
                'icon' => 'ri-time-line',
                'pill' => 'status-cancelled',
                'desc' => 'Khách yêu cầu hủy, chờ duyệt'
            ],
            'returned' => [
                'label' => 'Trả hàng/Hoàn tiền',
                'class' => 'text-warning',
                'icon' => 'ri-refund-2-line',
                'pill' => 'status-returned',
                'desc' => 'Đơn đã được trả lại hoặc hoàn tiền'
            ],
            'return_request' => [
                'label' => 'Yêu cầu trả hàng',
                'class' => 'text-warning',
                'icon' => 'ri-time-line',
                'pill' => 'status-returned',
                'desc' => 'Khách yêu cầu trả hàng, chờ duyệt'
            ],
        ];
        // Quy tắc chuyển trạng thái hợp lệ
        $statusTransitions = [
            'pending' => ['pending', 'processing', 'cancelled'],
            'processing' => ['processing', 'shipping', 'cancelled'],
            'shipping' => ['shipping', 'delivered', 'returned'], // Chỉ có thể chọn 'delivered' hoặc 'returned', không có 'completed'
            'delivered' => ['delivered', 'completed', 'returned'], // Chỉ từ 'delivered' mới được chọn 'completed'
            'completed' => ['completed', 'returned'],
            'cancelled' => ['cancelled'],
            'returned' => ['returned'],
            'cancel_request' => ['cancel_request', 'cancelled'],
            'return_request' => ['return_request', 'returned'],
        ];

        $paymentStatusLabels = [
            'paid' => 'Đã thanh toán',
            'unpaid' => 'Chưa thanh toán',
            'refunded' => 'Đã hoàn tiền',
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-shopping-bag-3-line me-2"></i>Quản lý đơn hàng</h4>
            </div>
        </div>
    </div>

    @php
        $totalOrders = $orderStats->total_orders ?? 0;
        $processingCount = ($orderStats->processing_orders ?? 0) + ($orderStats->shipping_orders ?? 0);
        $processingPercent = $totalOrders > 0
            ? max(5, min(100, ($processingCount / max(1, $totalOrders)) * 100))
            : 0;
        $completionPercent = $totalOrders > 0
            ? round((($orderStats->completed_orders ?? 0) / max(1, $totalOrders)) * 100)
            : 0;
    @endphp

    {{-- 🧮 Thống kê --}}
    <div class="row cursor-pointer">
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Tổng đơn</span>
                <span class="stat-value">{{ $orderStats->total_orders ?? 0 }}</span>
                <span class="stat-trend text-muted"><i class="ri-arrow-up-line text-success"></i> so với tuần trước</span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Đang xử lý / Giao hàng</span>
                <span class="stat-value text-warning">{{ $processingCount }}</span>
                <div class="progress progress-sm mt-2" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar"
                        style="<?php echo 'width: ' . $processingPercent . '%'; ?>"
                        aria-valuenow="{{ $processingPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Hoàn tất</span>
                <span class="stat-value text-success">{{ $orderStats->completed_orders ?? 0 }}</span>
                <span class="stat-trend text-success"><i class="ri-check-line me-1"></i>Tỉ lệ hoàn tất
                    {{ $completionPercent }}%</span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Bị hủy / Hoàn tiền</span>
                <span class="stat-value text-danger">{{ ($orderStats->cancelled_orders ?? 0) + ($orderStats->returned_orders ?? 0) }}</span>
                <span class="stat-trend text-muted"><i class="ri-alert-line text-danger"></i> Cần xử lý nhanh</span>
            </div>
        </div>
    </div>

    {{-- Yêu cầu hủy / trả hàng --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card request-orders-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0"><i class="ri-error-warning-line me-2"></i>Yêu cầu hủy / trả hàng</h5>
                        <span class="badge bg-warning text-dark">{{ $requestOrders->total() }}</span>
                    </div>
                    @if($requestOrders->isEmpty())
                        <p class="text-muted mb-0">Không có yêu cầu nào.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requestOrders as $ord)
                                <tr>
                                    <td>{{ $ord->code }}</td>
                                    <td>{{ $ord->full_name }}</td>
                                    <td>
                                        @if($ord->status === 'cancel_request')
                                            <span class="badge bg-danger-subtle text-danger">Yêu cầu hủy</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Yêu cầu trả</span>
                                        @endif
                                    </td>
                                    <td>{{ $ord->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                                <button type="button" 
                                                    class="btn btn-sm btn-info view-request-detail" 
                                                    data-order-code="{{ $ord->code }}"
                                                    data-reason="{{ $ord->cancel_reason ?? $ord->return_reason ?? 'Không có lý do' }}"
                                                    data-images='@json($ord->cancel_images ?? $ord->return_images ?? [])'
                                                    data-status="{{ $ord->status }}"
                                                    title="Xem chi tiết">
                                                <i class="ri-eye-line"></i> Xem
                                                </button>
                                        @if($ord->status === 'cancel_request')
                                                <form method="POST" action="{{ route('admin.orders.approveCancel', $ord) }}" class="d-inline approve-request-form" data-order-id="{{ $ord->id }}" data-type="cancel">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Duyệt hủy</button>
                                            </form>
                                        @else
                                                <form method="POST" action="{{ route('admin.orders.approveReturn', $ord) }}" class="d-inline approve-request-form" data-order-id="{{ $ord->id }}" data-type="return">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">Duyệt trả</button>
                                            </form>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        {{ $requestOrders->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 🔎 Bộ lọc --}}
    <div class="card orders-list-card" style="position: relative; z-index: 100;">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div>
                <h4 class="card-title mb-0"><i class="ri-file-list-3-line me-2"></i>Danh sách đơn hàng</h4>
                <span class="text-muted small">Theo dõi trạng thái và dòng tiền theo thời gian thực</span>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" id="toggleFilterBtn">
                    <i class="ri-filter-3-line"></i> Bộ lọc
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="ri-printer-line"></i> In báo cáo
                </button>
            </div>
        </div>

        {{-- Form lọc --}}
        <div class="card-body" id="filterForm" style="display: none;">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm nhanh</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Tên, email hoặc mã đơn">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Mã đơn</label>
                        <input type="text" name="code" value="{{ request('code') }}" class="form-control"
                            placeholder="VD: STX-0001">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tên khách hàng</label>
                        <input type="text" name="full_name" value="{{ request('full_name') }}" class="form-control"
                            placeholder="VD: Trần Minh">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái đơn</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Chờ giao hàng</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Trả hàng/Hoàn tiền</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán
                            </option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán
                            </option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Hoàn tiền
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Từ ngày</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Đến ngày</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex w-100 gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="ri-search-line"></i> Lọc
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-soft-secondary">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        {{-- 📋 Danh sách --}}
        <div class="card-body">
            @php
                $totalShipping  = $shippingOrders->total();   // Chờ xác nhận / xử lý / giao
                $totalCompleted = $completedOrders->total();  // Hoàn thành / đã giao
                $totalCancel    = $cancelOrders->total();     // Đã hủy
                $totalReturn    = $returnOrders->total();     // Trả hàng / hoàn tiền
                $totalAll       = $totalShipping + $totalCompleted + $totalCancel + $totalReturn;
            @endphp

            {{-- Thanh trạng thái + chọn kích thước trang --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div class="text-muted small">
                    @if ($totalAll > 0)
                        <span class="me-3">Tổng: {{ $totalAll }} đơn</span>
                        <span class="badge bg-warning-subtle text-warning me-2">
                            Chờ xác nhận / Xử lý / Giao: {{ $totalShipping }}
                        </span>
                        <span class="badge bg-success-subtle text-success me-2">
                            Hoàn thành / Đã giao: {{ $totalCompleted }}
                        </span>
                        <span class="badge bg-danger-subtle text-danger me-2">
                            Đã hủy: {{ $totalCancel }}
                        </span>
                        <span class="badge bg-info-subtle text-info">
                            Trả hàng / Hoàn tiền: {{ $totalReturn }}
                        </span>
                    @else
                        Không có đơn hàng nào khớp bộ lọc
                    @endif
                </div>
                <form method="GET" class="d-inline-flex align-items-center gap-2">
                    @foreach (request()->except('per_page', 'page') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="per_page" class="text-muted small mb-0">Hiển thị</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                {{ $size }} đơn / trang
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Tabs: chia 4 nhóm đơn hàng --}}
            <ul class="nav nav-pills mb-3" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-shipping-orders"
                            data-bs-toggle="tab" data-bs-target="#pane-shipping-orders"
                            type="button" role="tab" aria-controls="pane-shipping-orders" aria-selected="true">
                        Chờ xác nhận / Xử lý / Giao ({{ $totalShipping }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-completed-orders"
                            data-bs-toggle="tab" data-bs-target="#pane-completed-orders"
                            type="button" role="tab" aria-controls="pane-completed-orders" aria-selected="false">
                        Hoàn thành / Đã giao ({{ $totalCompleted }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-cancel-orders"
                            data-bs-toggle="tab" data-bs-target="#pane-cancel-orders"
                            type="button" role="tab" aria-controls="pane-cancel-orders" aria-selected="false">
                        Đã hủy ({{ $totalCancel }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-return-orders"
                            data-bs-toggle="tab" data-bs-target="#pane-return-orders"
                            type="button" role="tab" aria-controls="pane-return-orders" aria-selected="false">
                        Trả hàng / Hoàn tiền ({{ $totalReturn }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="orderTabsContent">
                {{-- TAB 1: Chờ xác nhận / Xử lý / Giao --}}
                <div class="tab-pane fade show active" id="pane-shipping-orders" role="tabpanel" aria-labelledby="tab-shipping-orders">
                    <div class="table-responsive table-card order-table-wrapper">
                        <table class="table align-middle text-center table-nowrap order-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Cập nhật</th>
                                <th>Người cập nhật</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse ($shippingOrders as $order)
                                @php
                                    $paymentStatusClass = match ($order->payment_status) {
                                        'paid' => 'bg-success-subtle text-success',
                                        'unpaid' => 'bg-warning-subtle text-warning',
                                        'refunded' => 'bg-info-subtle text-info',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                    $detailPayload = [
                                        'id' => $order->id,
                                        'code' => $order->code,
                                        'buyer_name' => $order->buyer_name,
                                        'buyer_email' => $order->buyer_email,
                                        'buyer_phone' => $order->buyer_phone,
                                        'full_name' => $order->full_name,
                                        'email' => $order->email,
                                        'phone' => $order->phone,
                                        'address' => $order->address,
                                        'city' => $order->city,
                                        'total' => number_format($order->total, 0, ',', '.'),
                                        'status' => $order->status,
                                        'payment_status' => $order->payment_status,
                                        'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                        'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                                        'updated_at' => $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '',
                                        'updated_by_name' => $order->updatedByUser->name ?? null,
                                        'updated_by_roles' => $order->updatedByUser ? $order->updatedByUser->roles->map(function($role) {
                                            return ['name' => $role->name, 'color' => $role->color ?? '#6c757d'];
                                        })->toArray() : [],
                                        'notes' => $order->note ?? 'Không có ghi chú',
                                        'subtotal' => number_format($order->subtotal ?? 0, 0, ',', '.'),
                                        'discount' => number_format($order->discount ?? 0, 0, ',', '.'),
                                        'tax_amount' => number_format($order->tax_amount ?? 0, 0, ',', '.'),
                                        'shipping_fee' => number_format($order->shipping_fee ?? 0, 0, ',', '.'),
                                        'items' => $order->items->map(function ($item) {
                                            // Lấy thông tin biến thể
                                            $variantInfo = [];
                                            if ($item->variant) {
                                                if ($item->variant->size) {
                                                    $variantInfo[] = 'Size: ' . $item->variant->size->name;
                                                }
                                                if ($item->variant->color) {
                                                    $variantInfo[] = 'Màu: ' . $item->variant->color->name;
                                                }
                                                if ($item->variant->texture) {
                                                    $variantInfo[] = 'Chất liệu: ' . $item->variant->texture->name;
                                                }
                                            }
                                            $variantText = !empty($variantInfo) ? implode(' - ', $variantInfo) : '';
                                            
                                            return [
                                                'name' => $item->product->name ?? 'Sản phẩm',
                                                'sku' => $item->variant->sku ?? ($item->product->sku ?? 'N/A'),
                                                'variant' => $variantText,
                                                'quantity' => $item->quantity,
                                                'price' => number_format($item->price ?? 0, 0, ',', '.'),
                                                'total' => number_format(($item->price ?? 0) * $item->quantity, 0, ',', '.'),
                                                'image' => $item->product->default_image_url ?? asset('client/images/product-01.jpg'),
                                            ];
                                        })->values()->all(),
                                    ];
                                        $currentStatusKey = $order->status ?? 'pending';
                                        $currentStatus = $statusStyles[$currentStatusKey] ?? $statusStyles['pending'];
                                        $allowedStatuses = $statusTransitions[$currentStatusKey] ?? [$currentStatusKey];
                                @endphp
                                <tr data-order-id="{{ $order->id }}">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->code }}</td>
                                    <td class="text-start">
                                        <div class="fw-bold">{{ $order->full_name }}</div>
                                        <small class="text-muted">{{ $order->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-dark text-body">
                                            {{ $order->items->count() }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-primary">{{ number_format($order->total) }}₫</td>
                                    <td>
                                        <span class="badge {{ $paymentStatusClass }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? strtoupper($order->payment_status ?? 'N/A') }}
                                        </span>
                                        <div class="small text-muted">
                                            {{ strtoupper($order->payment_method ?? 'COD') }}
                                        </div>
                                    </td>
                                    <td>
                                            <div class="status-control d-inline-flex align-items-center gap-2"
                                                 data-order-id="{{ $order->id }}"
                                                 data-current="{{ $currentStatusKey }}">
                                                <span class="status-pill {{ $currentStatus['pill'] }}">
                                                    <i class="{{ $currentStatus['icon'] }} me-1"></i>
                                                    {{ $currentStatus['label'] }}
                                                </span>
                                                <div class="dropdown dropup">
                                                    <button class="btn btn-light btn-icon btn-sm status-toggle" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-arrow-down-s-line"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end status-menu">
                                                        @foreach ($statusStyles as $key => $option)
                                                            @php
                                                                $isActive = $currentStatusKey === $key;
                                                                $disabled = !in_array($key, $allowedStatuses, true);
                                                                // Ẩn hoàn toàn option "completed" khi status là "shipping"
                                                                $shouldHide = ($currentStatusKey === 'shipping' && $key === 'completed');
                                                            @endphp
                                                            @if (!$shouldHide)
                                                            <button type="button"
                                                                    class="dropdown-item status-action d-flex justify-content-between align-items-start {{ $isActive ? 'active' : '' }} {{ $disabled ? 'disabled' : '' }}"
                                                                    data-status="{{ $key }}">
                                                                <span class="d-flex gap-2">
                                                                    <span class="status-dot {{ $option['pill'] }}"></span>
                                                                    <span>
                                                                        <span class="fw-semibold d-block">{{ $option['label'] }}</span>
                                                                        <small class="text-muted">{{ $option['desc'] }}</small>
                                                                    </span>
                                                                </span>
                                                                <i class="ri-check-line status-check {{ $isActive ? '' : 'opacity-0' }}"></i>
                                                            </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                        <td class="order-updater">
                                            @if($order->updatedByUser)
                                                <div>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ $order->updatedByUser->name ?? 'N/A' }}
                                                    </span>
                                                    @if($order->updatedByUser->roles && $order->updatedByUser->roles->isNotEmpty())
                                                        <br>
                                                        <small class="text-muted">
                                                            @foreach($order->updatedByUser->roles as $role)
                                                                <span class="badge" style="background-color: {{ $role->color ?? '#6c757d' }}20; color: {{ $role->color ?? '#6c757d' }}; border: 1px solid {{ $role->color ?? '#6c757d' }}40;">
                                                                    {{ $role->name }}
                                                                </span>
                                                            @endforeach
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-soft-primary view-order-detail"
                                                    data-order='@json($detailPayload)'>
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-soft-secondary"
                                                    onclick="window.print()">
                                                <i class="ri-printer-fill"></i>
                                            </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-muted">Không có đơn hàng đang xử lý / giao hàng trên trang này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang TAB 1 --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted small">
                            Trang: {{ $shippingOrders->currentPage() }} / {{ $shippingOrders->lastPage() }}
                        </div>
                        <div>
                            {{ $shippingOrders->onEachSide(1)->appends(request()->except('shipping_page', 'page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Đã hủy --}}
                <div class="tab-pane fade" id="pane-cancel-orders" role="tabpanel" aria-labelledby="tab-cancel-orders">
                    @if($cancelOrders->count())
                    <div class="table-responsive table-card order-table-wrapper">
                        <table class="table align-middle text-center table-nowrap order-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Cập nhật</th>
                                    <th>Người cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cancelOrders as $order)
                                    @php
                                        $paymentStatusClass = match ($order->payment_status) {
                                            'paid' => 'bg-success-subtle text-success',
                                            'unpaid' => 'bg-warning-subtle text-warning',
                                            'refunded' => 'bg-info-subtle text-info',
                                            default => 'bg-secondary-subtle text-secondary'
                                        };
                                        $detailPayload = [
                                            'id' => $order->id,
                                            'code' => $order->code,
                                            'buyer_name' => $order->buyer_name,
                                            'buyer_email' => $order->buyer_email,
                                            'buyer_phone' => $order->buyer_phone,
                                            'full_name' => $order->full_name,
                                            'email' => $order->email,
                                            'phone' => $order->phone,
                                            'address' => $order->address,
                                            'total' => number_format($order->total, 0, ',', '.'),
                                            'status' => $order->status,
                                            'payment_status' => $order->payment_status,
                                            'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                            'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                                            'updated_at' => $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '',
                                            'notes' => $order->note ?? 'Không có ghi chú',
                                            'items' => $order->items->map(function ($item) {
                                                // Lấy thông tin biến thể
                                                $variantInfo = [];
                                                if ($item->variant) {
                                                    if ($item->variant->size) {
                                                        $variantInfo[] = 'Size: ' . $item->variant->size->name;
                                                    }
                                                    if ($item->variant->color) {
                                                        $variantInfo[] = 'Màu: ' . $item->variant->color->name;
                                                    }
                                                    if ($item->variant->texture) {
                                                        $variantInfo[] = 'Chất liệu: ' . $item->variant->texture->name;
                                                    }
                                                }
                                                $variantText = !empty($variantInfo) ? implode(' - ', $variantInfo) : '';
                                                
                                                return [
                                                    'name' => $item->product->name ?? 'Sản phẩm',
                                                    'sku' => $item->variant->sku ?? ($item->product->sku ?? 'N/A'),
                                                    'variant' => $variantText,
                                                    'quantity' => $item->quantity,
                                                    'price' => number_format($item->price ?? 0, 0, ',', '.'),
                                                    'total' => number_format(($item->price ?? 0) * $item->quantity, 0, ',', '.'),
                                                    'image' => $item->product->default_image_url ?? asset('client/images/product-01.jpg'),
                                                ];
                                            })->values()->all(),
                                        ];
                                            $currentStatusKey = $order->status ?? 'pending';
                                            $currentStatus = $statusStyles[$currentStatusKey] ?? $statusStyles['pending'];
                                            $allowedStatuses = $statusTransitions[$currentStatusKey] ?? [$currentStatusKey];
                                        @endphp
                                    <tr data-order-id="{{ $order->id }}">
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->code }}</td>
                                        <td class="text-start">
                                            <div class="fw-bold">{{ $order->full_name }}</div>
                                            <small class="text-muted">{{ $order->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-dark text-body">
                                                {{ $order->items->count() }} sản phẩm
                                            </span>
                                        </td>
                                    <td class="fw-semibold text-primary">{{ number_format($order->total) }}₫</td>
                                    <td>
                                        <span class="badge {{ $paymentStatusClass }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? strtoupper($order->payment_status ?? 'N/A') }}
                                        </span>
                                        <div class="small text-muted">
                                            {{ strtoupper($order->payment_method ?? 'COD') }}
                                        </div>
                                    </td>
                                        <td>
                                        <div class="status-control d-inline-flex align-items-center gap-2"
                                            data-order-id="{{ $order->id }}"
                                            data-current="{{ $currentStatusKey }}">
                                            <span class="status-pill {{ $currentStatus['pill'] }}">
                                                <i class="{{ $currentStatus['icon'] }} me-1"></i>
                                                {{ $currentStatus['label'] }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-icon btn-sm status-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end status-menu">
                                                    @foreach ($statusStyles as $key => $option)
                                                        @php
                                                            $isActive = $currentStatusKey === $key;
                                                            $disabled = !in_array($key, $allowedStatuses, true);
                                                            // Ẩn hoàn toàn option "completed" khi status là "shipping"
                                                            $shouldHide = ($currentStatusKey === 'shipping' && $key === 'completed');
                                                        @endphp
                                                        @if (!$shouldHide)
                                                        <button type="button"
                                                            class="dropdown-item status-action d-flex justify-content-between align-items-start {{ $isActive ? 'active' : '' }} {{ $disabled ? 'disabled' : '' }}"
                                                            data-status="{{ $key }}">
                                                            <span class="d-flex gap-2">
                                                                <span class="status-dot {{ $option['pill'] }}"></span>
                                                                <span>
                                                                    <span class="fw-semibold d-block">{{ $option['label'] }}</span>
                                                                    <small class="text-muted">{{ $option['desc'] }}</small>
                                                                </span>
                                                            </span>
                                                            <i class="ri-check-line status-check {{ $isActive ? '' : 'opacity-0' }}"></i>
                                                        </button>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="order-updater">
                                        @if($order->updatedByUser)
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $order->updatedByUser->name ?? 'N/A' }}
                                                </span>
                                                @if($order->updatedByUser->roles && $order->updatedByUser->roles->isNotEmpty())
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach($order->updatedByUser->roles as $role)
                                                            <span class="badge" style="background-color: {{ $role->color ?? '#6c757d' }}20; color: {{ $role->color ?? '#6c757d' }}; border: 1px solid {{ $role->color ?? '#6c757d' }}40;">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-soft-primary view-order-detail"
                                                data-order='@json($detailPayload)'>
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-soft-secondary"
                                                onclick="window.print()">
                                                <i class="ri-printer-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted small">
                            Trang: {{ $cancelOrders->currentPage() }} / {{ $cancelOrders->lastPage() }}
                        </div>
                        <div>
                            {{ $cancelOrders->onEachSide(1)->appends(request()->except('cancel_page', 'page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                    @else
                        <div class="alert alert-info mb-0">Không có đơn đã hủy trên trang này.</div>
                    @endif
                </div>

                {{-- TAB 3: Trả hàng / Hoàn tiền --}}
                <div class="tab-pane fade" id="pane-return-orders" role="tabpanel" aria-labelledby="tab-return-orders">
                    @if($returnOrders->count())
                    <div class="table-responsive table-card order-table-wrapper">
                        <table class="table align-middle text-center table-nowrap order-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Cập nhật</th>
                                    <th>Người cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returnOrders as $order)
                                @php
                                    $paymentStatusClass = match ($order->payment_status) {
                                        'paid' => 'bg-success-subtle text-success',
                                        'unpaid' => 'bg-warning-subtle text-warning',
                                        'refunded' => 'bg-info-subtle text-info',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                    $detailPayload = [
                                        'id' => $order->id,
                                        'code' => $order->code,
                                        'buyer_name' => $order->buyer_name,
                                        'buyer_email' => $order->buyer_email,
                                        'buyer_phone' => $order->buyer_phone,
                                        'full_name' => $order->full_name,
                                        'email' => $order->email,
                                        'phone' => $order->phone,
                                        'address' => $order->address,
                                        'city' => $order->city,
                                        'total' => number_format($order->total, 0, ',', '.'),
                                        'status' => $order->status,
                                        'payment_status' => $order->payment_status,
                                        'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                        'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                                        'updated_at' => $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '',
                                        'updated_by_name' => $order->updatedByUser->name ?? null,
                                        'updated_by_roles' => $order->updatedByUser ? $order->updatedByUser->roles->map(function($role) {
                                            return ['name' => $role->name, 'color' => $role->color ?? '#6c757d'];
                                        })->toArray() : [],
                                        'notes' => $order->note ?? 'Không có ghi chú',
                                        'subtotal' => number_format($order->subtotal ?? 0, 0, ',', '.'),
                                        'discount' => number_format($order->discount ?? 0, 0, ',', '.'),
                                        'tax_amount' => number_format($order->tax_amount ?? 0, 0, ',', '.'),
                                        'shipping_fee' => number_format($order->shipping_fee ?? 0, 0, ',', '.'),
                                        'items' => $order->items->map(function ($item) {
                                            // Lấy thông tin biến thể
                                            $variantInfo = [];
                                            if ($item->variant) {
                                                if ($item->variant->size) {
                                                    $variantInfo[] = 'Size: ' . $item->variant->size->name;
                                                }
                                                if ($item->variant->color) {
                                                    $variantInfo[] = 'Màu: ' . $item->variant->color->name;
                                                }
                                                if ($item->variant->texture) {
                                                    $variantInfo[] = 'Chất liệu: ' . $item->variant->texture->name;
                                                }
                                            }
                                            $variantText = !empty($variantInfo) ? implode(' - ', $variantInfo) : '';
                                            
                                            return [
                                                'name' => $item->product->name ?? 'Sản phẩm',
                                                'sku' => $item->variant->sku ?? ($item->product->sku ?? 'N/A'),
                                                'variant' => $variantText,
                                                'quantity' => $item->quantity,
                                                'price' => number_format($item->price ?? 0, 0, ',', '.'),
                                                'total' => number_format(($item->price ?? 0) * $item->quantity, 0, ',', '.'),
                                                'image' => $item->product->default_image_url ?? asset('client/images/product-01.jpg'),
                                            ];
                                        })->values()->all(),
                                    ];
                                    $currentStatusKey = $order->status ?? 'returned';
                                    $currentStatus = $statusStyles[$currentStatusKey] ?? $statusStyles['returned'];
                                    $allowedStatuses = $statusTransitions[$currentStatusKey] ?? [$currentStatusKey];
                                @endphp
                                <tr data-order-id="{{ $order->id }}">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->code }}</td>
                                    <td class="text-start">
                                        <div class="fw-bold">{{ $order->full_name }}</div>
                                        <small class="text-muted">{{ $order->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-dark text-body">
                                            {{ $order->items->count() }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-primary">{{ number_format($order->total) }}₫</td>
                                    <td>
                                        <span class="badge {{ $paymentStatusClass }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? strtoupper($order->payment_status ?? 'N/A') }}
                                        </span>
                                        <div class="small text-muted">
                                            {{ strtoupper($order->payment_method ?? 'COD') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-control d-inline-flex align-items-center gap-2"
                                             data-order-id="{{ $order->id }}"
                                             data-current="{{ $currentStatusKey }}">
                                            <span class="status-pill {{ $currentStatus['pill'] }}">
                                                <i class="{{ $currentStatus['icon'] }} me-1"></i>
                                                {{ $currentStatus['label'] }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-icon btn-sm status-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end status-menu">
                                                    @foreach ($statusStyles as $key => $option)
                                                        @php
                                                            $isActive = $currentStatusKey === $key;
                                                            $disabled = !in_array($key, $allowedStatuses, true);
                                                            // Ẩn hoàn toàn option "completed" khi status là "shipping"
                                                            $shouldHide = ($currentStatusKey === 'shipping' && $key === 'completed');
                                                        @endphp
                                                        @if (!$shouldHide)
                                                        <button type="button"
                                                                class="dropdown-item status-action d-flex justify-content-between align-items-start {{ $isActive ? 'active' : '' }} {{ $disabled ? 'disabled' : '' }}"
                                                                data-status="{{ $key }}">
                                                            <span class="d-flex gap-2">
                                                                <span class="status-dot {{ $option['pill'] }}"></span>
                                                                <span>
                                                                    <span class="fw-semibold d-block">{{ $option['label'] }}</span>
                                                                    <small class="text-muted">{{ $option['desc'] }}</small>
                                                                </span>
                                                            </span>
                                                            <i class="ri-check-line status-check {{ $isActive ? '' : 'opacity-0' }}"></i>
                                                        </button>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="order-updater">
                                        @if($order->updatedByUser)
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $order->updatedByUser->name ?? 'N/A' }}
                                                </span>
                                                @if($order->updatedByUser->roles && $order->updatedByUser->roles->isNotEmpty())
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach($order->updatedByUser->roles as $role)
                                                            <span class="badge" style="background-color: {{ $role->color ?? '#6c757d' }}20; color: {{ $role->color ?? '#6c757d' }}; border: 1px solid {{ $role->color ?? '#6c757d' }}40;">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-soft-primary view-order-detail"
                                                    data-order='@json($detailPayload)'>
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-soft-secondary"
                                                    onclick="window.print()">
                                                <i class="ri-printer-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-muted">Không có đơn trả hàng / hoàn tiền trên trang này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Phân trang TAB 3 --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted small">
                            Trang: {{ $returnOrders->currentPage() }} / {{ $returnOrders->lastPage() }}
                        </div>
                        <div>
                            {{ $returnOrders->onEachSide(1)->appends(request()->except('return_page', 'page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                    @else
                        <div class="alert alert-info mb-0">Không có đơn trả hàng / hoàn tiền trên trang này.</div>
                    @endif
                </div>

                {{-- TAB 2 (mới): Hoàn thành / Đã giao --}}
                <div class="tab-pane fade" id="pane-completed-orders" role="tabpanel" aria-labelledby="tab-completed-orders">
                    <div class="table-responsive table-card order-table-wrapper">
                        <table class="table align-middle text-center table-nowrap order-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Cập nhật</th>
                                    <th>Người cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($completedOrders as $order)
                                @php
                                    $paymentStatusClass = match ($order->payment_status) {
                                        'paid' => 'bg-success-subtle text-success',
                                        'unpaid' => 'bg-warning-subtle text-warning',
                                        'refunded' => 'bg-info-subtle text-info',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                    $detailPayload = [
                                        'id' => $order->id,
                                        'code' => $order->code,
                                        'buyer_name' => $order->buyer_name,
                                        'buyer_email' => $order->buyer_email,
                                        'buyer_phone' => $order->buyer_phone,
                                        'full_name' => $order->full_name,
                                        'email' => $order->email,
                                        'phone' => $order->phone,
                                        'address' => $order->address,
                                        'city' => $order->city,
                                        'total' => number_format($order->total, 0, ',', '.'),
                                        'status' => $order->status,
                                        'payment_status' => $order->payment_status,
                                        'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                        'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                                        'updated_at' => $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '',
                                        'updated_by_name' => $order->updatedByUser->name ?? null,
                                        'updated_by_roles' => $order->updatedByUser ? $order->updatedByUser->roles->map(function($role) {
                                            return ['name' => $role->name, 'color' => $role->color ?? '#6c757d'];
                                        })->toArray() : [],
                                        'notes' => $order->note ?? 'Không có ghi chú',
                                        'subtotal' => number_format($order->subtotal ?? 0, 0, ',', '.'),
                                        'discount' => number_format($order->discount ?? 0, 0, ',', '.'),
                                        'tax_amount' => number_format($order->tax_amount ?? 0, 0, ',', '.'),
                                        'shipping_fee' => number_format($order->shipping_fee ?? 0, 0, ',', '.'),
                                        'items' => $order->items->map(function ($item) {
                                            // Lấy thông tin biến thể
                                            $variantInfo = [];
                                            if ($item->variant) {
                                                if ($item->variant->size) {
                                                    $variantInfo[] = 'Size: ' . $item->variant->size->name;
                                                }
                                                if ($item->variant->color) {
                                                    $variantInfo[] = 'Màu: ' . $item->variant->color->name;
                                                }
                                                if ($item->variant->texture) {
                                                    $variantInfo[] = 'Chất liệu: ' . $item->variant->texture->name;
                                                }
                                            }
                                            $variantText = !empty($variantInfo) ? implode(' - ', $variantInfo) : '';
                                            
                                            return [
                                                'name' => $item->product->name ?? 'Sản phẩm',
                                                'sku' => $item->variant->sku ?? ($item->product->sku ?? 'N/A'),
                                                'variant' => $variantText,
                                                'quantity' => $item->quantity,
                                                'price' => number_format($item->price ?? 0, 0, ',', '.'),
                                                'total' => number_format(($item->price ?? 0) * $item->quantity, 0, ',', '.'),
                                                'image' => $item->product->default_image_url ?? asset('client/images/product-01.jpg'),
                                            ];
                                        })->values()->all(),
                                    ];
                                    $currentStatusKey = $order->status ?? 'completed';
                                    $currentStatus = $statusStyles[$currentStatusKey] ?? $statusStyles['completed'];
                                    $allowedStatuses = $statusTransitions[$currentStatusKey] ?? [$currentStatusKey];
                                @endphp
                                <tr data-order-id="{{ $order->id }}">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->code }}</td>
                                    <td class="text-start">
                                        <div class="fw-bold">{{ $order->full_name }}</div>
                                        <small class="text-muted">{{ $order->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-dark text-body">
                                            {{ $order->items->count() }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-primary">{{ number_format($order->total) }}₫</td>
                                    <td>
                                        <span class="badge {{ $paymentStatusClass }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? strtoupper($order->payment_status ?? 'N/A') }}
                                        </span>
                                        <div class="small text-muted">
                                            {{ strtoupper($order->payment_method ?? 'COD') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-control d-inline-flex align-items-center gap-2"
                                             data-order-id="{{ $order->id }}"
                                             data-current="{{ $currentStatusKey }}">
                                            <span class="status-pill {{ $currentStatus['pill'] }}">
                                                <i class="{{ $currentStatus['icon'] }} me-1"></i>
                                                {{ $currentStatus['label'] }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-icon btn-sm status-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end status-menu">
                                                    @foreach ($statusStyles as $key => $option)
                                                        @php
                                                            $isActive = $currentStatusKey === $key;
                                                            $disabled = !in_array($key, $allowedStatuses, true);
                                                            // Ẩn hoàn toàn option "completed" khi status là "shipping"
                                                            $shouldHide = ($currentStatusKey === 'shipping' && $key === 'completed');
                                                        @endphp
                                                        @if (!$shouldHide)
                                                        <button type="button"
                                                                class="dropdown-item status-action d-flex justify-content-between align-items-start {{ $isActive ? 'active' : '' }} {{ $disabled ? 'disabled' : '' }}"
                                                                data-status="{{ $key }}">
                                                            <span class="d-flex gap-2">
                                                                <span class="status-dot {{ $option['pill'] }}"></span>
                                                                <span>
                                                                    <span class="fw-semibold d-block">{{ $option['label'] }}</span>
                                                                    <small class="text-muted">{{ $option['desc'] }}</small>
                                                                </span>
                                                            </span>
                                                            <i class="ri-check-line status-check {{ $isActive ? '' : 'opacity-0' }}"></i>
                                                        </button>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="order-updater">
                                        @if($order->updatedByUser)
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $order->updatedByUser->name ?? 'N/A' }}
                                                </span>
                                                @if($order->updatedByUser->roles && $order->updatedByUser->roles->isNotEmpty())
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach($order->updatedByUser->roles as $role)
                                                            <span class="badge" style="background-color: {{ $role->color ?? '#6c757d' }}20; color: {{ $role->color ?? '#6c757d' }}; border: 1px solid {{ $role->color ?? '#6c757d' }}40;">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-soft-primary view-order-detail"
                                                    data-order='@json($detailPayload)'>
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-soft-secondary"
                                                    onclick="window.print()">
                                                <i class="ri-printer-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-muted">Không có đơn đã hoàn thành / đã giao trên trang này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang TAB 2 --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted small">
                            Trang: {{ $completedOrders->currentPage() }} / {{ $completedOrders->lastPage() }}
                        </div>
                        <div>
                            {{ $completedOrders->onEachSide(1)->appends(request()->except('completed_page', 'page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal xem chi tiết yêu cầu hủy/trả hàng --}}
    <div class="modal fade" id="requestDetailModal" tabindex="-1" aria-labelledby="requestDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestDetailModalLabel">Chi tiết yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">Mã đơn hàng:</label>
                        <div id="requestOrderCode" class="fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">Trạng thái:</label>
                        <div id="requestStatus"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">Lý do:</label>
                        <div id="requestReason" class="p-3 bg-light rounded" style="white-space: pre-wrap; min-height: 80px;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">Ảnh minh họa:</label>
                        <div id="requestImages" class="d-flex flex-wrap gap-3 mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="orderDetailModalLabel"><i class="ri-file-text-line me-2"></i>Chi tiết đơn hàng</h5>
                        <small class="text-muted">Theo dõi trạng thái xử lý theo thời gian thực</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="mb-3">
                                    <div class="order-detail-label">Mã đơn hàng</div>
                                    <div class="order-detail-value" id="detailCode">#</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Thông tin người đặt</div>
                                    <div class="order-detail-value" id="detailBuyerName">-</div>
                                    <div class="text-muted" id="detailBuyerEmail">-</div>
                                    <div class="text-muted" id="detailBuyerPhone">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Thông tin người nhận</div>
                                    <div class="order-detail-value" id="detailReceiverName">-</div>
                                    <div class="text-muted" id="detailReceiverEmail">-</div>
                                    <div class="text-muted" id="detailReceiverPhone">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Địa chỉ người nhận</div>
                                    <div class="text-body" id="detailAddress">-</div>
                                    <div class="text-muted small" id="detailCity">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Thanh toán</div>
                                    <div class="order-detail-value" id="detailPaymentStatus">-</div>
                                    <div class="text-muted" id="detailPaymentMethod">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Người cập nhật</div>
                                    <div class="text-body" id="detailUpdatedBy">
                                        <span class="badge bg-primary-subtle text-primary" id="detailUpdatedByName">-</span>
                                        <div id="detailUpdatedByRoles" class="mt-1"></div>
                                    </div>
                                    <div class="text-muted small" id="detailUpdatedAt">-</div>
                                </div>
                                <div>
                                    <div class="order-detail-label">Ghi chú</div>
                                    <div class="text-body" id="detailNotes">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="order-detail-label">Trạng thái xử lý</div>
                                <div class="timeline-step" data-step="pending">
                                    <span class="dot"></span>
                                    <div>
                                        <div class="fw-semibold">Chờ xác nhận</div>
                                        <small class="text-muted">Đang đợi xác nhận từ nhân viên CSKH</small>
                                    </div>
                                </div>
                                <div class="timeline-step" data-step="processing">
                                    <span class="dot"></span>
                                    <div>
                                        <div class="fw-semibold">Đang xử lý</div>
                                        <small class="text-muted">Chuẩn bị hàng & đóng gói</small>
                                    </div>
                                </div>
                                <div class="timeline-step" data-step="completed">
                                    <span class="dot"></span>
                                    <div>
                                        <div class="fw-semibold">Hoàn tất</div>
                                        <small class="text-muted">Đơn đã giao thành công</small>
                                    </div>
                                </div>
                                <div class="timeline-step" data-step="cancelled">
                                    <span class="dot"></span>
                                    <div>
                                        <div class="fw-semibold text-danger">Đã hủy</div>
                                        <small class="text-muted">Đơn hàng bị hủy theo yêu cầu</small>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="order-detail-label">Danh sách sản phẩm</div>
                                    <span class="badge bg-soft-primary" id="detailCreatedAt">-</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>SL</th>
                                                <th>Giá</th>
                                                <th>Tổng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailItems">
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Tạm tính</span>
                                        <span id="detailSubtotal">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Giảm giá</span>
                                        <span id="detailDiscount">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Thuế</span>
                                        <span id="detailTax">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Phí vận chuyển</span>
                                        <span id="detailShipping">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
                                        <span>Tổng cộng</span>
                                        <span id="detailTotal">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer print-hidden">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="printOrderDetail()">In phiếu giao hàng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Load Laravel Echo và Pusher --}}
    @vite(['resources/js/app.js'])
    <script>
        // Force dropdown trạng thái luôn render với z-index cao và position fixed
        document.addEventListener('DOMContentLoaded', function() {
            // Lắng nghe tất cả dropdown show event
            document.addEventListener('show.bs.dropdown', function(e) {
                const button = e.target.closest('.status-toggle');
                if (button) {
                    setTimeout(() => {
                        // Tìm dropdown menu (có thể là nextElementSibling hoặc trong dropdown container)
                        const dropdown = button.closest('.dropdown');
                        if (dropdown) {
                            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                            if (dropdownMenu) {
                                dropdownMenu.style.zIndex = '99999';
                                dropdownMenu.style.position = 'fixed';
                                // Đảm bảo nó không bị che bởi bất kỳ card nào
                                const rect = button.getBoundingClientRect();
                                const menuRect = dropdownMenu.getBoundingClientRect();
                                // Nếu dropdown bị che ở trên, điều chỉnh top
                                if (menuRect.top < 0) {
                                    dropdownMenu.style.top = (rect.top - menuRect.height) + 'px';
                                }
                            }
                        }
                    }, 50);
                }
            });
        });
        function updateStatus(orderId, status) {
            // Validate inputs
            if (!orderId || !status) {
                toastr.error('Thiếu thông tin đơn hàng hoặc trạng thái!');
                return Promise.reject(new Error('Missing orderId or status'));
            }

            const url = "{{ route('admin.orders.updateStatus', ':id') }}".replace(':id', orderId);
            console.log('Updating order status:', { orderId, status, url });
            
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status })
            })
                .then(async res => {
                    // Kiểm tra status code
                    if (!res.ok) {
                        // Đọc response để lấy thông báo lỗi
                        let errorMessage = `Lỗi ${res.status}: ${res.statusText}`;
                        try {
                            const errorData = await res.json();
                            errorMessage = errorData.message || errorMessage;
                            console.error('Error response:', errorData);
                        } catch (e) {
                            console.error('Could not parse error response:', e);
                        }
                        throw new Error(errorMessage);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.message) {
                    toastr.success(data.message);
                    } else {
                        toastr.success('Cập nhật trạng thái đơn hàng thành công!');
                    }
                    // Không cần reload nữa vì đã có realtime update
                    // Chỉ reload nếu cần thiết (ví dụ: order chuyển sang tab khác)
                    // setTimeout(() => {
                    //     window.location.reload();
                    // }, 1000);
                    return data;
                })
                .catch(err => {
                    const errorMessage = err.message || 'Lỗi khi cập nhật trạng thái đơn hàng!';
                    toastr.error(errorMessage);
                    console.error('Error updating order status:', err);
                    throw err;
                });
        }

        function printOrderDetail() {
            const modal = document.getElementById('orderDetailModal');
            if (!modal) return;
            const printable = modal.querySelector('.modal-body');
            if (!printable) return;

            // Clone chỉ phần nội dung body, tránh nhân đôi modal header/footer
            const clone = printable.cloneNode(true);
            clone.querySelectorAll('.print-hidden').forEach(el => el.remove());

            const styleLinks = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
                .map(el => el.outerHTML)
                .join('\n');

            const inlinePrintCss = `
                <style>
                    body { font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif; margin: 24px; color: #0f172a; }
                    h3 { margin: 0 0 8px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
                    th { text-transform: uppercase; letter-spacing: .05em; font-size: 12px; color: #94a3b8; }
                    .status-pill { font-size: 13px; }
                    .row { display: flex; flex-wrap: wrap; gap: 16px; }
                    .col-lg-4 { flex: 0 0 32%; max-width: 32%; }
                    .col-lg-8 { flex: 0 0 64%; max-width: 64%; }
                    @media (max-width: 768px) {
                        .col-lg-4, .col-lg-8 { flex: 0 0 100%; max-width: 100%; }
                    }
                </style>
            `;

            const printWindow = window.open('', '', 'width=900,height=650');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Chi tiết đơn hàng</title>
                        ${styleLinks}
                        ${inlinePrintCss}
                    </head>
                    <body>
                        <h3>Chi tiết đơn hàng</h3>
                        ${clone.outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 400);
        }
    </script>
    <script>
        const detailModalEl = document.getElementById('orderDetailModal');
        const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;
        function resetTimeline(status) {
            document.querySelectorAll('.timeline-step').forEach(step => {
                step.classList.remove('active');
                if (step.dataset.step === status || (status === 'cancelled' && step.dataset.step === 'cancelled')) {
                    step.classList.add('active');
                } else if (status === 'completed' && (step.dataset.step === 'pending' || step.dataset.step === 'processing' || step.dataset.step === 'completed')) {
                    step.classList.add('active');
                } else if (status === 'processing' && (step.dataset.step === 'pending' || step.dataset.step === 'processing')) {
                    step.classList.add('active');
                }
            });
        }
        const statusMeta = {
            pending:   { label: 'Chờ xác nhận', class: 'text-warning', icon: 'ri-time-line',          pill: 'status-pending',   desc: 'Đơn mới, chờ nhân viên xác nhận' },
            processing:{ label: 'Đang xử lý',   class: 'text-primary', icon: 'ri-loader-4-line',     pill: 'status-processing',desc: 'Đang chuẩn bị & đóng gói tại kho' },
            shipping:  { label: 'Chờ giao hàng',class: 'text-info',    icon: 'ri-truck-line',        pill: 'status-shipping',  desc: 'Đã bàn giao cho đơn vị vận chuyển' },
            delivered: { label: 'Đã giao',      class: 'text-success', icon: 'ri-checkbox-circle-line', pill: 'status-completed', desc: 'Đơn đã giao đến khách' },
            completed: { label: 'Hoàn thành',   class: 'text-success', icon: 'ri-check-double-line', pill: 'status-completed', desc: 'Đơn đã giao thành công' },
            cancelled: { label: 'Đã hủy',       class: 'text-danger',  icon: 'ri-close-line',        pill: 'status-cancelled', desc: 'Đơn bị hủy theo yêu cầu' },
            cancel_request: { label: 'Yêu cầu hủy', class: 'text-danger', icon: 'ri-time-line',      pill: 'status-cancelled', desc: 'Khách yêu cầu hủy, chờ duyệt' },
            returned:  { label: 'Trả hàng/Hoàn tiền', class: 'text-warning', icon: 'ri-refund-2-line', pill: 'status-returned', desc: 'Đơn đã được trả lại hoặc hoàn tiền' },
            return_request: { label: 'Yêu cầu trả hàng', class: 'text-warning', icon: 'ri-time-line', pill: 'status-returned', desc: 'Khách yêu cầu trả hàng, chờ duyệt' },
        };

        const statusTransitions = {
            pending:   ['pending', 'processing', 'cancelled'],
            processing:['processing', 'shipping', 'cancelled'],
            shipping:  ['shipping', 'delivered', 'returned'], // Chỉ có thể chọn 'delivered' hoặc 'returned', không có 'completed'
            delivered: ['delivered', 'completed', 'returned'], // Chỉ từ 'delivered' mới được chọn 'completed'
            completed: ['completed', 'returned'],
            cancelled: ['cancelled'],
            returned:  ['returned'],
            cancel_request: ['cancel_request', 'cancelled'],
            return_request: ['return_request', 'returned'],
        };

        function applyStatusUI(control, status) {
            const meta = statusMeta[status] || statusMeta.pending;
            const allowed = statusTransitions[status] || [status];
            control.dataset.current = status;
            control.dataset.allowed = JSON.stringify(allowed);

            const pill = control.querySelector('.status-pill');
            if (pill) {
                pill.className = `status-pill ${meta.pill}`;
                pill.innerHTML = `<i class="${meta.icon} me-1"></i>${meta.label}`;
            }

            control.querySelectorAll('.status-action').forEach(action => {
                const optionStatus = action.dataset.status;
                const isActive = optionStatus === status;
                const isDisabled = !allowed.includes(optionStatus);
                action.classList.toggle('active', isActive);
                action.classList.toggle('disabled', isDisabled);
                const checkIcon = action.querySelector('.status-check');
                if (checkIcon) {
                    checkIcon.classList.toggle('opacity-0', !isActive);
                }
            });
        }

        document.querySelectorAll('.status-control').forEach(control => {
            const current = control.dataset.current || 'pending';
            applyStatusUI(control, current);

            control.querySelectorAll('.status-action').forEach(action => {
                action.addEventListener('click', () => {
                    const targetStatus = action.dataset.status;
                    const allowed = JSON.parse(control.dataset.allowed || '[]');
                    const currentStatus = control.dataset.current || 'pending';
                    if (!allowed.includes(targetStatus) || targetStatus === currentStatus) {
                        return;
                    }

                    const toggleBtn = control.querySelector('.status-toggle');
                    toggleBtn && (toggleBtn.disabled = true);

                    updateStatus(control.dataset.orderId, targetStatus)
                        .then(() => {
                            setTimeout(() => window.location.reload(), 800);
                        })
                        .finally(() => {
                            toggleBtn && (toggleBtn.disabled = false);
                        });
                });
            });
        });

        const paymentStatusLabels = {
            paid: 'Đã thanh toán',
            unpaid: 'Chưa thanh toán',
            refunded: 'Đã hoàn tiền',
        };

        document.querySelectorAll('.view-order-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const data = JSON.parse(btn.dataset.order);
                document.getElementById('detailCode').textContent = data.code;
                
                // Thông tin người đặt
                document.getElementById('detailBuyerName').textContent = data.buyer_name || '—';
                document.getElementById('detailBuyerEmail').textContent = data.buyer_email || '—';
                document.getElementById('detailBuyerPhone').textContent = data.buyer_phone ? 'ĐT: ' + data.buyer_phone : '—';
                
                // Thông tin người nhận
                document.getElementById('detailReceiverName').textContent = data.full_name || '—';
                document.getElementById('detailReceiverEmail').textContent = data.email || '—';
                document.getElementById('detailReceiverPhone').textContent = data.phone ? 'ĐT: ' + data.phone : '—';
                
                // Hiển thị địa chỉ người nhận
                const addressParts = [];
                if (data.address) {
                    addressParts.push(data.address);
                }
                if (data.city) {
                    addressParts.push(data.city);
                }
                document.getElementById('detailAddress').textContent = addressParts.length > 0 ? addressParts.join(', ') : 'Không cung cấp';
                document.getElementById('detailPaymentStatus').textContent =
                    paymentStatusLabels[data.payment_status] || (data.payment_status || '—');
                document.getElementById('detailPaymentMethod').textContent = 'Phương thức: ' + data.payment_method;
                
                // Hiển thị người cập nhật
                const updatedByNameEl = document.getElementById('detailUpdatedByName');
                const updatedByRolesEl = document.getElementById('detailUpdatedByRoles');
                const updatedAtEl = document.getElementById('detailUpdatedAt');
                if (data.updated_by_name) {
                    updatedByNameEl.textContent = data.updated_by_name;
                    updatedByNameEl.className = 'badge bg-primary-subtle text-primary';
                    
                    // Hiển thị roles
                    if (data.updated_by_roles && data.updated_by_roles.length > 0) {
                        updatedByRolesEl.innerHTML = '<small class="text-muted">' + 
                            data.updated_by_roles.map(role => {
                                const roleName = typeof role === 'string' ? role : role.name;
                                const roleColor = typeof role === 'object' ? (role.color || '#6c757d') : '#6c757d';
                                return `<span class="badge me-1" style="background-color: ${roleColor}20; color: ${roleColor}; border: 1px solid ${roleColor}40;">${roleName}</span>`;
                            }).join('') + 
                            '</small>';
                    } else {
                        updatedByRolesEl.innerHTML = '';
                    }
                    
                    updatedAtEl.textContent = 'Cập nhật lúc: ' + (data.updated_at || '—');
                } else {
                    updatedByNameEl.textContent = 'Chưa có';
                    updatedByRolesEl.innerHTML = '';
                    updatedByNameEl.className = 'badge bg-secondary-subtle text-secondary';
                    updatedAtEl.textContent = '—';
                }
                
                document.getElementById('detailNotes').textContent = data.notes || 'Không có ghi chú';
                document.getElementById('detailCreatedAt').textContent = 'Tạo lúc ' + data.created_at;
                document.getElementById('detailSubtotal').textContent = data.subtotal ? data.subtotal + ' ₫' : '0 ₫';
                document.getElementById('detailDiscount').textContent = data.discount ? '- ' + data.discount + ' ₫' : '0 ₫';
                document.getElementById('detailTax').textContent = data.tax_amount ? data.tax_amount + ' ₫' : '0 ₫';
                document.getElementById('detailShipping').textContent = data.shipping_fee ? data.shipping_fee + ' ₫' : '0 ₫';
                document.getElementById('detailTotal').textContent = data.total ? data.total + ' ₫' : '0 ₫';

                const itemsTbody = document.getElementById('detailItems');
                itemsTbody.innerHTML = '';
                if (!data.items || data.items.length === 0) {
                    itemsTbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Không có sản phẩm</td></tr>';
                } else {
                    data.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="${item.image}" class="product-thumb" alt="${item.name}">
                                    <div>
                                        <div class="fw-semibold">${item.name}</div>
                                        <small class="text-muted">SKU: ${item.sku || 'N/A'}</small>
                                        ${item.variant ? `<div class="text-muted small mt-1" style="font-size: 11px; color: #6c757d;">${item.variant}</div>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>x${item.quantity}</td>
                            <td>${item.price} ₫</td>
                            <td class="fw-semibold">${item.total} ₫</td>
                        `;
                        itemsTbody.appendChild(row);
                    });
                }

                resetTimeline(data.status);
                detailModal && detailModal.show();
            });
        });

        const filterBtn = document.getElementById('toggleFilterBtn');
        if (filterBtn) {
            filterBtn.addEventListener('click', () => {
                $('#filterForm').slideToggle(200);
            });
        }

        // Xử lý xem chi tiết yêu cầu hủy/trả hàng
        const requestDetailModalEl = document.getElementById('requestDetailModal');
        const requestDetailModal = requestDetailModalEl ? new bootstrap.Modal(requestDetailModalEl) : null;

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-request-detail');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const orderCode = btn.dataset.orderCode || '';
            const reason = btn.dataset.reason || 'Không có lý do';
            const status = btn.dataset.status || '';
            let images = [];
            
            try {
                images = JSON.parse(btn.dataset.images || '[]');
            } catch (e) {
                console.error('Error parsing images:', e);
            }

            // Cập nhật nội dung modal
            document.getElementById('requestOrderCode').textContent = orderCode;
            
            // Hiển thị trạng thái
            const statusEl = document.getElementById('requestStatus');
            if (status === 'cancel_request') {
                statusEl.innerHTML = '<span class="badge bg-danger-subtle text-danger">Yêu cầu hủy</span>';
            } else if (status === 'return_request') {
                statusEl.innerHTML = '<span class="badge bg-warning-subtle text-warning">Yêu cầu trả hàng</span>';
            } else {
                statusEl.textContent = status;
            }

            // Hiển thị lý do
            document.getElementById('requestReason').textContent = reason;

            // Hiển thị ảnh
            const imagesContainer = document.getElementById('requestImages');
            imagesContainer.innerHTML = '';
            
            if (images && images.length > 0) {
                const baseUrl = '{{ url("/storage") }}';
                images.forEach((img, index) => {
                    const imageUrl = baseUrl + '/' + img;
                    const imgDiv = document.createElement('div');
                    imgDiv.className = 'text-center';
                    imgDiv.innerHTML = `
                        <a href="${imageUrl}" target="_blank" class="text-decoration-none">
                            <img src="${imageUrl}" 
                                 alt="Ảnh ${index + 1}" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px; max-height: 200px; object-fit: cover; cursor: pointer;"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27200%27 height=%27200%27%3E%3Crect width=%27200%27 height=%27200%27 fill=%27%23f0f0f0%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27 font-size=%2714%27 fill=%27%23999%27%3EKhông thể tải ảnh%3C/text%3E%3C/svg%3E';">
                        </a>
                        <div class="small text-muted mt-1">Ảnh ${index + 1}</div>
                    `;
                    imagesContainer.appendChild(imgDiv);
                });
            } else {
                imagesContainer.innerHTML = '<span class="text-muted">Không có ảnh</span>';
            }

            // Hiển thị modal
            if (requestDetailModal) {
                requestDetailModal.show();
            }
        });
    </script>
    <script>
        // Helper function để lấy status meta (phải định nghĩa trước khi sử dụng)
        function getStatusMeta(status) {
            const statusMetaMap = {
                pending:   { label: 'Chờ xác nhận', class: 'bg-warning-subtle text-warning', icon: 'ri-time-line' },
                processing:{ label: 'Đang xử lý',   class: 'bg-primary-subtle text-primary', icon: 'ri-loader-4-line' },
                shipping:  { label: 'Chờ giao hàng',class: 'bg-info-subtle text-info', icon: 'ri-truck-line' },
                delivered: { label: 'Đã giao',      class: 'bg-success-subtle text-success', icon: 'ri-checkbox-circle-line' },
                completed: { label: 'Hoàn thành',   class: 'bg-success-subtle text-success', icon: 'ri-check-double-line' },
                cancelled: { label: 'Đã hủy',       class: 'bg-danger-subtle text-danger', icon: 'ri-close-line' },
                cancel_request: { label: 'Yêu cầu hủy', class: 'bg-danger-subtle text-danger', icon: 'ri-time-line' },
                returned:  { label: 'Trả hàng/Hoàn tiền', class: 'bg-warning-subtle text-warning', icon: 'ri-refund-2-line' },
                return_request: { label: 'Yêu cầu trả hàng', class: 'bg-warning-subtle text-warning', icon: 'ri-time-line' },
            };
            return statusMetaMap[status] || { label: status, class: 'bg-secondary-subtle text-secondary', icon: 'ri-question-line' };
        }
        
        function getStatusLabel(status) {
            return getStatusMeta(status).label;
        }
        
        // Hàm cập nhật số lượng trong các tab
        function updateTabCounts() {
            // Đếm số đơn hàng trong mỗi tab
            const shippingTab = document.getElementById('pane-shipping-orders');
            const completedTab = document.getElementById('pane-completed-orders');
            const cancelTab = document.getElementById('pane-cancel-orders');
            const returnTab = document.getElementById('pane-return-orders');
            
            const shippingCount = shippingTab ? shippingTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const completedCount = completedTab ? completedTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const cancelCount = cancelTab ? cancelTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const returnCount = returnTab ? returnTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            
            // Cập nhật badge trong các tab button
            const shippingBadge = document.querySelector('#tab-shipping-orders .badge');
            const completedBadge = document.querySelector('#tab-completed-orders .badge');
            const cancelBadge = document.querySelector('#tab-cancel-orders .badge');
            const returnBadge = document.querySelector('#tab-return-orders .badge');
            
            if (shippingBadge) shippingBadge.textContent = shippingCount;
            if (completedBadge) completedBadge.textContent = completedCount;
            if (cancelBadge) cancelBadge.textContent = cancelCount;
            if (returnBadge) returnBadge.textContent = returnCount;
        }
        
        // Hàm cập nhật thống kê
        function updateOrderStats() {
            // Đếm số đơn hàng trong các tab
            const shippingTab = document.getElementById('pane-shipping-orders');
            const completedTab = document.getElementById('pane-completed-orders');
            const cancelTab = document.getElementById('pane-cancel-orders');
            const returnTab = document.getElementById('pane-return-orders');
            
            const shippingCount = shippingTab ? shippingTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const completedCount = completedTab ? completedTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const cancelCount = cancelTab ? cancelTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            const returnCount = returnTab ? returnTab.querySelectorAll('tbody tr[data-order-id]').length : 0;
            
            const totalOrders = shippingCount + completedCount + cancelCount + returnCount;
            const processingCount = shippingCount;
            const cancelledReturnedCount = cancelCount + returnCount;
            
            // Cập nhật thống kê
            const totalOrdersEl = document.querySelector('.stat-card .stat-value');
            const processingEl = document.querySelectorAll('.stat-card .stat-value.text-warning');
            const completedEl = document.querySelectorAll('.stat-card .stat-value.text-success');
            const cancelledEl = document.querySelectorAll('.stat-card .stat-value.text-danger');
            
            // Cập nhật tổng đơn (card đầu tiên)
            if (totalOrdersEl && document.querySelector('.stat-card:first-child .stat-value') === totalOrdersEl) {
                totalOrdersEl.textContent = totalOrders;
            }
            
            // Cập nhật đang xử lý (card thứ 2)
            if (processingEl.length > 0) {
                processingEl[0].textContent = processingCount;
                // Cập nhật progress bar
                const progressBar = processingEl[0].closest('.stat-card')?.querySelector('.progress-bar');
                if (progressBar && totalOrders > 0) {
                    const percent = Math.max(5, Math.min(100, (processingCount / totalOrders) * 100));
                    progressBar.style.width = percent + '%';
                    progressBar.setAttribute('aria-valuenow', percent);
                }
            }
            
            // Cập nhật hoàn tất (card thứ 3)
            if (completedEl.length > 0) {
                completedEl[0].textContent = completedCount;
                // Cập nhật tỉ lệ hoàn tất
                const trendEl = completedEl[0].closest('.stat-card')?.querySelector('.stat-trend');
                if (trendEl && totalOrders > 0) {
                    const percent = Math.round((completedCount / totalOrders) * 100);
                    trendEl.innerHTML = `<i class="ri-check-line me-1"></i>Tỉ lệ hoàn tất ${percent}%`;
                }
            }
            
            // Cập nhật hủy/hoàn tiền (card thứ 4)
            if (cancelledEl.length > 0) {
                cancelledEl[0].textContent = cancelledReturnedCount;
            }
        }

        // ========== REALTIME ORDER UPDATES ==========
        // Chỉ chạy khi window.Echo đã được load (từ bootstrap.js)
        if (typeof window.Echo !== 'undefined') {
            // Lắng nghe event cập nhật trạng thái đơn hàng
            window.Echo.channel('orders')
                .listen('.order.status.updated', (e) => {
                    console.log('Order status updated:', e);
                    
                    const orderData = e;
                    const orderId = orderData.id;
                    const newStatus = orderData.status;
                    
                    // Tìm tất cả các row chứa order này trong các bảng
                    const orderRows = document.querySelectorAll(`tr[data-order-id="${orderId}"]`);
                    
                    if (orderRows.length === 0) {
                        // Nếu không tìm thấy order trong trang hiện tại, có thể là order mới hoặc đã bị filter
                        // Hiển thị thông báo
                        toastr.info(`Đơn hàng #${orderData.code} đã được cập nhật trạng thái: ${getStatusLabel(newStatus)}`);
                        return;
                    }
                    
                    // Lưu trạng thái cũ để xác định tab cũ
                    const oldStatus = orderRows.length > 0 ? 
                        (orderRows[0].querySelector('.order-status-badge')?.textContent?.trim() || '') : '';
                    
                    // Cập nhật từng row
                    orderRows.forEach(row => {
                        // Cập nhật status badge
                        const statusBadge = row.querySelector('.order-status-badge');
                        if (statusBadge) {
                            const statusMeta = getStatusMeta(newStatus);
                            statusBadge.className = `badge order-status-badge ${statusMeta.class}`;
                            statusBadge.innerHTML = `<i class="${statusMeta.icon} me-1"></i>${statusMeta.label}`;
                        }
                        
                        // Cập nhật status control nếu có
                        const statusControl = row.querySelector(`.status-control[data-order-id="${orderId}"]`);
                        if (statusControl) {
                            applyStatusUI(statusControl, newStatus);
                        }
                        
                        // Cập nhật người cập nhật
                        const updaterCell = row.querySelector('.order-updater');
                        if (updaterCell && orderData.updated_by_name) {
                            let rolesHtml = '';
                            if (orderData.updated_by_roles && orderData.updated_by_roles.length > 0) {
                                rolesHtml = '<br><small class="text-muted">' + 
                                    orderData.updated_by_roles.map(role => {
                                        const roleName = typeof role === 'string' ? role : role.name;
                                        const roleColor = typeof role === 'object' ? (role.color || '#6c757d') : '#6c757d';
                                        return `<span class="badge me-1" style="background-color: ${roleColor}20; color: ${roleColor}; border: 1px solid ${roleColor}40;">${roleName}</span>`;
                                    }).join('') + 
                                    '</small>';
                            }
                            updaterCell.innerHTML = `
                                <div>
                                    <span class="badge bg-primary-subtle text-primary">${orderData.updated_by_name}</span>
                                    ${rolesHtml}
                                </div>
                            `;
                        }
                        
                        // Cập nhật payment status nếu có
                        if (orderData.payment_status) {
                            const paymentBadge = row.querySelector('.payment-status-badge');
                            if (paymentBadge) {
                                const paymentLabels = {
                                    paid: { label: 'Đã thanh toán', class: 'bg-success-subtle text-success' },
                                    unpaid: { label: 'Chưa thanh toán', class: 'bg-warning-subtle text-warning' },
                                    refunded: { label: 'Đã hoàn tiền', class: 'bg-info-subtle text-info' },
                                };
                                const paymentMeta = paymentLabels[orderData.payment_status] || { label: orderData.payment_status, class: 'bg-secondary-subtle text-secondary' };
                                paymentBadge.className = `badge payment-status-badge ${paymentMeta.class}`;
                                paymentBadge.textContent = paymentMeta.label;
                            }
                        }
                    });
                    
                    // Xác định tab mới dựa trên status mới
                    let targetTab = null;
                    let targetPane = null;
                    if (['pending', 'processing', 'shipping'].includes(newStatus)) {
                        targetTab = 'tab-shipping-orders';
                        targetPane = 'pane-shipping-orders';
                    } else if (['completed', 'delivered'].includes(newStatus)) {
                        targetTab = 'tab-completed-orders';
                        targetPane = 'pane-completed-orders';
                    } else if (newStatus === 'cancelled' || newStatus === 'cancel_request') {
                        targetTab = 'tab-cancel-orders';
                        targetPane = 'pane-cancel-orders';
                    } else if (newStatus === 'returned' || newStatus === 'return_request') {
                        targetTab = 'tab-return-orders';
                        targetPane = 'pane-return-orders';
                    }
                    
                    // Nếu status thay đổi và cần di chuyển tab, thực hiện di chuyển
                    if (targetTab && orderRows.length > 0) {
                        const currentTab = orderRows[0].closest('.tab-pane');
                        const targetTabPane = document.getElementById(targetPane);
                        
                        if (currentTab && targetTabPane && currentTab.id !== targetPane) {
                            // Di chuyển row sang tab mới
                            orderRows.forEach(row => {
                                const tbody = targetTabPane.querySelector('tbody');
                                if (tbody) {
                                    tbody.appendChild(row);
                                }
                            });
                            
                            // Cập nhật số lượng trong các tab
                            updateTabCounts();
                            
                            // Highlight tab mới
                            const newTabButton = document.getElementById(targetTab);
                            if (newTabButton) {
                                newTabButton.classList.add('border-primary', 'border-2');
                                setTimeout(() => {
                                    newTabButton.classList.remove('border-primary', 'border-2');
                                }, 3000);
                            }
                        }
                    }
                    
                    // Cập nhật thống kê
                    updateOrderStats();
                    
                    // Hiển thị thông báo đặc biệt cho yêu cầu hủy/trả hàng
                    const statusMeta = getStatusMeta(newStatus);
                    const isRequestStatus = newStatus === 'return_request' || newStatus === 'cancel_request';
                    
                    if (isRequestStatus) {
                        // Thông báo nổi bật cho yêu cầu hủy/trả hàng
                        const requestType = newStatus === 'return_request' ? 'Trả hàng' : 'Hủy đơn';
                        const requestIcon = newStatus === 'return_request' ? '🔄' : '❌';
                        const orderUrl = "{{ route('admin.orders.index') }}?code=" + orderData.code;
                        
                        const message = `${requestIcon} <strong>Yêu cầu ${requestType}</strong><br>` +
                            `Đơn hàng: <strong>#${orderData.code}</strong><br>` +
                            `Khách hàng: <strong>${orderData.full_name}</strong><br>` +
                            `<span class="text-muted">Vui lòng xem và xử lý yêu cầu này</span>`;
                        
                        toastr.warning(message, `⚠️ Yêu cầu ${requestType} mới`, {
                            timeOut: 10000,
                            progressBar: true,
                            closeButton: true,
                            onclick: function() {
                                window.location.href = orderUrl;
                            },
                            onShown: function() {
                                // Highlight tab "Yêu cầu hủy/trả" nếu có
                                const requestTab = document.querySelector('[data-bs-target="#pane-request-orders"]');
                                if (requestTab) {
                                    requestTab.classList.add('border-warning', 'border-2');
                                    setTimeout(() => {
                                        requestTab.classList.remove('border-warning', 'border-2');
                                    }, 3000);
                                }
                            }
                        });
                        
                        // Cập nhật badge số lượng yêu cầu đang chờ
                        fetch("{{ route('admin.orders.pendingRequestsCount') }}")
                            .then(res => res.json())
                            .then(data => {
                                const badge = document.querySelector('.badge.bg-warning.text-dark');
                                if (badge && data.count !== undefined) {
                                    badge.textContent = data.count;
                                }
                            })
                            .catch(err => console.error('Error updating request count:', err));
                        
                        // Scroll đến phần "Yêu cầu hủy/trả hàng" và highlight
                        setTimeout(() => {
                            // Tìm card chứa "Yêu cầu hủy / trả hàng"
                            const cards = document.querySelectorAll('.card');
                            let requestSection = null;
                            for (let card of cards) {
                                const heading = card.querySelector('h5');
                                if (heading && heading.textContent.includes('Yêu cầu hủy / trả hàng')) {
                                    requestSection = card;
                                    break;
                                }
                            }
                            if (requestSection) {
                                requestSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                requestSection.classList.add('border-warning', 'border-3', 'shadow-lg');
                                setTimeout(() => {
                                    requestSection.classList.remove('border-warning', 'border-3', 'shadow-lg');
                                }, 5000);
                            }
                        }, 500);
                    } else {
                        // Thông báo thông thường cho các trạng thái khác
                        toastr.success(`Đơn hàng #${orderData.code} đã được cập nhật: ${statusMeta.label}`, 'Cập nhật realtime', {
                            timeOut: 3000,
                            progressBar: true
                        });
                    }
                    
                    // Nếu đang mở modal chi tiết của order này, cập nhật luôn
                    const detailModal = document.getElementById('orderDetailModal');
                    if (detailModal && detailModal.classList.contains('show')) {
                        const detailCode = document.getElementById('detailCode');
                        if (detailCode && detailCode.textContent === orderData.code) {
                            // Cập nhật timeline trong modal
                            resetTimeline(newStatus);
                            
                            // Cập nhật status trong modal nếu có
                            const modalStatusBadge = detailModal.querySelector('.modal-status-badge');
                            if (modalStatusBadge) {
                                const statusMeta = getStatusMeta(newStatus);
                                modalStatusBadge.className = `badge modal-status-badge ${statusMeta.class}`;
                                modalStatusBadge.innerHTML = `<i class="${statusMeta.icon} me-1"></i>${statusMeta.label}`;
                            }
                        }
                    }
                });
            
            // Lắng nghe event đơn hàng mới
            window.Echo.channel('orders')
                .listen('.order.created', (e) => {
                    console.log('New order created:', e);
                    
                    const orderData = e;
                    
                    // Hiển thị thông báo với nút xem chi tiết
                    const orderUrl = "{{ route('admin.orders.index') }}?code=" + orderData.code;
                    const message = `Đơn hàng mới #${orderData.code}<br>` +
                        `<strong>${orderData.full_name}</strong><br>` +
                        `<span class="text-muted">${orderData.items_count} sản phẩm</span> - ` +
                        `<strong class="text-primary">${new Intl.NumberFormat('vi-VN').format(orderData.total)} ₫</strong>`;
                    
                    toastr.success(message, '🆕 Đơn hàng mới', {
                        timeOut: 8000,
                        progressBar: true,
                        closeButton: true,
                        onclick: function() {
                            window.location.href = orderUrl;
                        },
                        onShown: function() {
                            // Thêm class để có thể click vào thông báo
                            const toast = $('.toast:last');
                            toast.css('cursor', 'pointer');
                            toast.on('click', function() {
                                window.location.href = orderUrl;
                            });
                        }
                    });
                    
                    // Không reload trang nữa - dùng realtime update
                    // Nếu có đơn hàng mới hoặc yêu cầu mới, sẽ được cập nhật qua event realtime
                });
            
            console.log('✅ Realtime order updates enabled');
            console.log('📡 Listening on channel: orders');
            console.log('📡 Listening for events: .order.status.updated, .order.created');
            
            // Test connection
            window.Echo.connector.pusher.connection.bind('connected', function() {
                console.log('✅ Pusher connected successfully');
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', function() {
                console.warn('⚠️ Pusher disconnected');
            });
            
            window.Echo.connector.pusher.connection.bind('error', function(err) {
                console.error('❌ Pusher connection error:', err);
            });
            
            // Test channel subscription
            const ordersChannel = window.Echo.channel('orders');
            ordersChannel.subscribed(() => {
                console.log('✅ Successfully subscribed to orders channel');
            });
            
            ordersChannel.error((error) => {
                console.error('❌ Error subscribing to orders channel:', error);
            });
        } else {
            console.warn('⚠️ Laravel Echo not loaded. Realtime updates disabled.');
            console.warn('💡 Make sure Vite app.js is loaded và BROADCAST_CONNECTION trong .env được set đúng (pusher/reverb).');
        }
    </script>
    <script>
        // Chuyển form approve cancel/return sang AJAX để không reload trang
        document.addEventListener('DOMContentLoaded', function() {
            const approveForms = document.querySelectorAll('.approve-request-form');
            approveForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const form = this;
                    const action = form.action;
                    const orderId = form.dataset.orderId;
                    const type = form.dataset.type;
                    const button = form.querySelector('button[type="submit"]');
                    const originalText = button.textContent;
                    
                    // Disable button
                    button.disabled = true;
                    button.textContent = 'Đang xử lý...';
                    
                    // Lấy CSRF token
                    const csrfToken = form.querySelector('input[name="_token"]').value;
                    
                    // Tạo FormData từ form
                    const formData = new FormData(form);
                    
                    // Gửi AJAX request
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const errorData = await res.json().catch(() => ({}));
                            throw new Error(errorData.message || `Lỗi ${res.status}: ${res.statusText}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        const message = data.message || (type === 'cancel' ? 'Đã duyệt yêu cầu hủy đơn thành công!' : 'Đã duyệt yêu cầu trả hàng thành công!');
                        toastr.success(message);
                        
                        // Tìm row chứa order này và xóa nó (vì đã được duyệt)
                        const row = form.closest('tr');
                        if (row) {
                            // Fade out animation
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                
                                // Cập nhật badge số lượng
                                const badge = document.querySelector('.badge.bg-warning.text-dark');
                                if (badge) {
                                    const currentCount = parseInt(badge.textContent) || 0;
                                    const newCount = Math.max(0, currentCount - 1);
                                    if (newCount > 0) {
                                        badge.textContent = newCount;
                                    } else {
                                        badge.classList.add('d-none');
                                        badge.textContent = '0';
                                    }
                                }
                                
                                // Kiểm tra xem còn yêu cầu nào không
                                const tbody = document.querySelector('table tbody');
                                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                    const card = document.querySelector('.card');
                                    if (card) {
                                        const emptyMessage = document.createElement('p');
                                        emptyMessage.className = 'text-muted mb-0';
                                        emptyMessage.textContent = 'Không có yêu cầu nào.';
                                        tbody.parentElement.parentElement.insertBefore(emptyMessage, tbody.parentElement);
                                    }
                                }
                            }, 300);
                        }
                    })
                    .catch(err => {
                        toastr.error(err.message || 'Có lỗi xảy ra khi duyệt yêu cầu!');
                        button.disabled = false;
                        button.textContent = originalText;
                    });
                });
            });
        });

    </script>
@endpush
