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

                                @forelse ($activeOrders as $order)
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
                                        'full_name' => $order->full_name,
                                        'email' => $order->email,
                                        'phone' => $order->phone,
                                        'address' => $order->address,

                                        'subtotal' => number_format($order->subtotal ?? 0, 0, ',', '.'),
                                        'tax_amount' => number_format($order->tax_amount ?? 0, 0, ',', '.'),
                                        'voucher_discount' => number_format($order->voucher_discount ?? 0, 0, ',', '.'),
                                        'total' => number_format($order->total ?? 0, 0, ',', '.'),

                                        'payment_status' => $order->payment_status,
                                        'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                        'created_at' => optional($order->created_at)->format('d/m/Y H:i'),
                                        'updated_at' => optional($order->updated_at)->format('d/m/Y H:i'),
                                        'notes' => $order->note ?? 'Không có ghi chú',

                                        'items' => $order->items->map(function ($item) {
                                            return [
                                                'name' => $item->product->name ?? 'Sản phẩm',
                                                'sku' => $item->product->sku ?? 'N/A',
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
                                                            @endphp
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
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-soft-primary view-order-detail"
                                                        data-order='@json($detailPayload)'>
                                                    <i class="ri-file-text-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-soft-secondary"
                                                        onclick="window.print()">
                                                    <i class="ri-printer-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-muted">Không có đơn hàng đang xử lý / giao hàng trên trang này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang TAB 1 --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted small">
                            Trang: {{ $activeOrders->currentPage() }} / {{ $activeOrders->lastPage() }}
                        </div>
                        <div>
                            {{ $activeOrders->onEachSide(1)->appends(request()->except('active_page', 'page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Đơn hoàn thành / hủy / hoàn tiền --}}
                <div class="tab-pane fade" id="pane-archived-orders" role="tabpanel" aria-labelledby="tab-archived-orders">
                    <div class="table-responsive table-card">
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
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($archivedOrders as $order)
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
                                            'full_name' => $order->full_name,
                                            'email' => $order->email,
                                            'phone' => $order->phone,
                                            'address' => $order->address,

                                            'subtotal' => number_format($order->subtotal ?? 0, 0, ',', '.'),
                                            'tax_amount' => number_format($order->tax_amount ?? 0, 0, ',', '.'),
                                            'voucher_discount' => number_format($order->voucher_discount ?? 0, 0, ',', '.'),
                                            'total' => number_format($order->total ?? 0, 0, ',', '.'),

                                            'payment_status' => $order->payment_status,
                                            'payment_method' => strtoupper($order->payment_method ?? 'COD'),
                                            'created_at' => optional($order->created_at)->format('d/m/Y H:i'),
                                            'updated_at' => optional($order->updated_at)->format('d/m/Y H:i'),
                                            'notes' => $order->note ?? 'Không có ghi chú',

                                            'items' => $order->items->map(function ($item) {
                                                return [
                                                    'name' => $item->product->name ?? 'Sản phẩm',
                                                    'sku' => $item->product->sku ?? 'N/A',
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
                                    <tr>
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
                                                {{ ucfirst($order->payment_status ?? 'N/A') }}
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
                                                        @endphp
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
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>

                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


    {{-- Detail Modal --}}
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
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
                                    <div class="order-detail-label">Khách hàng</div>
                                    <div class="order-detail-value" id="detailCustomer">-</div>
                                    <div class="text-muted" id="detailEmail">-</div>
                                    <div class="text-muted" id="detailPhone">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Địa chỉ giao hàng</div>
                                    <div class="text-body" id="detailAddress">-</div>
                                </div>
                                <div class="mb-3">
                                    <div class="order-detail-label">Thanh toán</div>
                                    <div class="order-detail-value" id="detailPaymentStatus">-</div>
                                    <div class="text-muted" id="detailPaymentMethod">-</div>
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
                                <div>
                                    <div class="order-detail-label">Danh sách sản phẩm</div>
                                    <!-- 🔽 PHẦN TIỀN CHI TIẾT -->
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Tạm tính</span>
                                    <span id="detailSubtotal">0 ₫</span>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Thuế</span>
                                    <span id="detailTax">0 ₫</span>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Voucher</span>
                                    <span class="text-danger">
                                        -<span id="detailVoucher">0</span> ₫
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between fw-semibold border-top pt-2 mt-2">
                                    <span>Tổng phải trả</span>
                                    <span class="text-primary" id="detailFinalTotal">0 ₫</span>
                                </div>
                            </div>
                                </div>
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
            pending:   { label: 'Chờ xác nhận', class: 'text-warning', icon: 'ri-time-line',        pill: 'status-pending',   desc: 'Đơn mới, chờ nhân viên xác nhận' },
            processing:{ label: 'Đang xử lý',   class: 'text-primary', icon: 'ri-loader-4-line',    pill: 'status-processing',desc: 'Đang chuẩn bị & đóng gói tại kho' },
            shipping:  { label: 'Chờ giao hàng',class: 'text-info',    icon: 'ri-truck-line',       pill: 'status-shipping',  desc: 'Đã bàn giao cho đơn vị vận chuyển' },
            completed: { label: 'Hoàn thành',   class: 'text-success', icon: 'ri-check-double-line',pill: 'status-completed', desc: 'Đơn đã giao thành công' },
            cancelled: { label: 'Đã hủy',       class: 'text-danger',  icon: 'ri-close-line',       pill: 'status-cancelled', desc: 'Đơn bị hủy theo yêu cầu' },
            returned:  { label: 'Trả hàng/Hoàn tiền', class: 'text-warning', icon: 'ri-refund-2-line', pill: 'status-returned', desc: 'Đơn đã được trả lại hoặc hoàn tiền' },
        };

        const statusTransitions = {
            pending:   ['pending', 'processing', 'cancelled'],
            processing:['processing', 'shipping', 'cancelled'],
            shipping:  ['shipping', 'completed', 'returned'],
            completed: ['completed'],
            cancelled: ['cancelled'],
            returned:  ['returned'],
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
                            applyStatusUI(control, targetStatus);
                            if (toggleBtn) {
                                const dropdownInstance = bootstrap.Dropdown.getInstance(toggleBtn);
                                dropdownInstance && dropdownInstance.hide();
                            }
                        })
                        .finally(() => {
                            toggleBtn && (toggleBtn.disabled = false);
                        });
                });
            });
        });

        document.querySelectorAll('.view-order-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const data = JSON.parse(btn.dataset.order);
                document.getElementById('detailCode').textContent = data.code;
                document.getElementById('detailCustomer').textContent = data.full_name;
                document.getElementById('detailEmail').textContent = data.email || '—';
                document.getElementById('detailPhone').textContent = data.phone || '—';
                document.getElementById('detailAddress').textContent = data.address || 'Không cung cấp';
                document.getElementById('detailPaymentStatus').textContent = (data.payment_status || 'pending').toUpperCase();
                document.getElementById('detailPaymentMethod').textContent = 'Phương thức: ' + data.payment_method;
                document.getElementById('detailNotes').textContent = data.notes || 'Không có ghi chú';
                document.getElementById('detailCreatedAt').textContent = 'Tạo lúc ' + data.created_at;
                //document.getElementById('detailTotal').textContent = 'Tổng: ' + data.total + ' ₫';
                document.getElementById('detailSubtotal').innerText = data.subtotal + ' ₫';
                document.getElementById('detailTax').innerText = data.tax_amount + ' ₫';
                document.getElementById('detailVoucher').innerText = data.voucher_discount + ' ₫';
                document.getElementById('detailFinalTotal').innerText = data.total + ' ₫';


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
                                        <small class="text-muted">SKU: ${item.sku}</small>
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
        };
    </script>
@endpush
