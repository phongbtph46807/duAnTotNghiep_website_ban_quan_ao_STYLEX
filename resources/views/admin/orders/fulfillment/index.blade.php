@extends('admin.layouts.app')

@section('title', 'Đóng gói & Giao hàng')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">📦 Đóng gói & Giao hàng</h1>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-info">Đơn hàng bán</span>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ {{ $message }}</strong>
            
            @if ($insufficientItems = Session::get('insufficient_items'))
                <hr>
                <p class="mb-2"><strong>Hàng không đủ - Cần mua hàng:</strong></p>
                <ul class="mb-3">
                    @foreach ($insufficientItems as $item)
                        <li>
                            <strong>{{ $item['variant_name'] }}</strong>
                            - Cần: {{ $item['required'] }}, Có: {{ $item['available'] }}, Thiếu: <span class="text-danger">{{ $item['shortage'] }}</span>
                        </li>
                    @endforeach
                </ul>
                
                <p class="mb-0 text-muted"><small>💡 Vào menu <strong>Nhập kho</strong> để tạo đề xuất mua hàng</small></p>
            @endif
            
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã đơn, khách hàng..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Chờ chọn kho</option>
                        <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="PICKING" {{ request('status') === 'PICKING' ? 'selected' : '' }}>Đang lấy hàng</option>
                        <option value="PACKED" {{ request('status') === 'PACKED' ? 'selected' : '' }}>Đã đóng gói</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="warehouse" class="form-select form-select-sm">
                        <option value="">-- Tất cả kho --</option>
                        @foreach ($warehouses ?? [] as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">🔍 Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Kho</th>
                        <th>Sản phẩm (SKU/GTIN)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $status = $order->picking?->status ?? 'PENDING';
                            $statusClass = match($status) {
                                'PENDING' => 'badge bg-secondary',
                                'CONFIRMED' => 'badge bg-info',
                                'PICKING' => 'badge bg-warning',
                                'PACKED' => 'badge bg-primary',
                                'CANCELLED' => 'badge bg-danger',
                                default => 'badge bg-light'
                            };
                            $statusText = match($status) {
                                'PENDING' => '⏳ Chờ chọn kho',
                                'CONFIRMED' => '✓ Đã xác nhận',
                                'PICKING' => '📋 Đang lấy hàng',
                                'PACKED' => '📦 Đã đóng gói',
                                'CANCELLED' => '❌ Đã hủy',
                                default => 'Không xác định'
                            };
                        @endphp
                        <tr>
                            <td><strong>#{{ $order->code }}</strong></td>
                            <td>{{ $order->buyer_name }}</td>
                            <td><strong class="text-danger">{{ number_format($order->total) }} đ</strong></td>
                            <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
                            <td><small class="badge bg-light text-dark">{{ $order->picking?->warehouse?->name ?? '-' }}</small></td>
                            <td>
                                <small>
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->variant->product->name }} - {{ $item->variant->name }}</div>
                                        <div class="text-muted">SKU: {{ $item->variant->sku }} | GTIN: {{ $item->variant->gtin ?? '-' }} | SL: {{ $item->quantity }}</div>
                                    @endforeach
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.orders.fulfillment.show', $order) }}" class="btn btn-outline-secondary btn-sm">
                                        👁️ Chi tiết
                                    </a>

                                    @if (!$order->picking || $order->picking->status === 'PENDING')
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#warehouseModal{{ $order->id }}">
                                            ✓ Chọn kho
                                        </button>
                                    @endif

                                    @if ($order->picking && $order->picking->status === 'CONFIRMED')
                                        <form action="{{ route('admin.orders.fulfillment.pack', $order->picking) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Đóng gói & giao vận chuyển?')">
                                                📦 Đóng gói
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal chọn kho -->
                        <div class="modal fade" id="warehouseModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chọn kho xuất - #{{ $order->code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.orders.fulfillment.confirm', $order) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Chọn kho <span class="text-danger">*</span></label>
                                                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                                    <option value="">-- Chọn kho --</option>
                                                    @foreach ($warehouses as $wh)
                                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('warehouse_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-success">Xác nhận & Reserve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Không có đơn hàng nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
