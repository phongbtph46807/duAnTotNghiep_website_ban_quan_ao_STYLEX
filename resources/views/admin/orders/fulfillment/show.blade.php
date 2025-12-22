@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->code)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">📦 Chi tiết đơn hàng #{{ $order->code }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.orders.fulfillment.index') }}" class="btn btn-secondary btn-sm">← Quay lại</a>
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
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Mã đơn:</strong> #{{ $order->code }}</p>
                            <p><strong>Khách hàng:</strong> {{ $order->buyer_name ?? $order->full_name ?? 'N/A' }}</p>
                            <p><strong>Điện thoại:</strong> {{ $order->buyer_phone ?? $order->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $order->buyer_email ?? $order->email ?? '-' }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Sản phẩm</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>SKU</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->variant->product->name }}</td>
                                    <td>Size: {{ $item->variant->size?->name ?? '-' }} | Màu: {{ $item->variant->color?->name ?? '-' }}</td>
                                    <td><small>{{ $item->variant->sku }}</small></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price) }} d</td>
                                    <td><strong>{{ number_format($item->quantity * $item->price) }} d</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Trạng thái & Thao tác -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Trạng thái</h5>
                </div>
                <div class="card-body">
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
                            'PENDING' => 'Chờ chọn kho',
                            'CONFIRMED' => 'Đã xác nhận',
                            'PICKING' => 'Đang lấy hàng',
                            'PACKED' => 'Đã đóng gói',
                            'CANCELLED' => 'Đã hủy',
                            default => 'Không xác định'
                        };
                    @endphp
                    <p class="mb-3">
                        <strong>Picking:</strong> <span class="{{ $statusClass }}">{{ $statusText }}</span>
                    </p>
                    <p class="mb-3">
                        <strong>Order:</strong> 
                        <span class="badge bg-{{ $order->status === 'processing' ? 'warning' : ($order->status === 'shipping' ? 'info' : ($order->status === 'delivered' ? 'success' : 'secondary')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Kho:</strong> {{ $order->picking?->warehouse?->name ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    @if ($order->picking?->status === 'PENDING')
                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                            Chọn kho
                        </button>
                    @elseif ($order->picking?->status === 'CONFIRMED')
                        <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#packingModal">
                            Đóng gói
                        </button>
                    @elseif ($order->picking?->status === 'PACKED')
                        <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#shippingModal">
                            Giao hàng
                        </button>
                    @elseif ($order->picking?->status === 'SHIPPED')
                        <span class="badge bg-success w-100 p-2">✓ Đã giao hàng</span>
                    @endif
                    <a href="{{ route('admin.orders.fulfillment.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        Quay lại danh sách
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Tổng tiền</h5>
                </div>
                <div class="card-body">
                    <h4 class="text-danger">{{ number_format($order->total) }} đ</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chọn kho -->
    <div class="modal fade" id="warehouseModal" tabindex="-1">
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
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach (\App\Models\Warehouse::where('operational_status', 'ACTIVE')->get() as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận đóng gói -->
    <div class="modal fade" id="packingModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận đóng gói</h5>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn đóng gói đơn hàng <strong>#{{ $order->code }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('admin.orders.fulfillment.pack', $order->picking) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Xác nhận đóng gói</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận giao hàng -->
    <div class="modal fade" id="shippingModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận giao hàng</h5>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn đơn hàng <strong>#{{ $order->code }}</strong> đã được giao thành công?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('admin.orders.fulfillment.ship', $order) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">Xác nhận giao hàng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
