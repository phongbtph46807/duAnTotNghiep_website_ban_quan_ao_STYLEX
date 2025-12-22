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
                            <p><strong>Khách hàng:</strong> {{ $order->buyer_name }}</p>
                            <p><strong>Điện thoại:</strong> {{ $order->buyer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $order->buyer_email }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->buyer_address }}</p>
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
                                    <td>{{ $item->variant->name }}</td>
                                    <td><small>{{ $item->variant->sku }}</small></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price) }} đ</td>
                                    <td><strong>{{ number_format($item->quantity * $item->price) }} đ</strong></td>
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
                            'PENDING' => '⏳ Chờ chọn kho',
                            'CONFIRMED' => '✓ Đã xác nhận',
                            'PICKING' => '📋 Đang lấy hàng',
                            'PACKED' => '📦 Đã đóng gói',
                            'CANCELLED' => '❌ Đã hủy',
                            default => 'Không xác định'
                        };
                    @endphp
                    <p class="mb-3">
                        <strong>Picking:</strong> <span class="{{ $statusClass }}">{{ $statusText }}</span>
                    </p>
                    <p class="mb-3">
                        <strong>Order:</strong> 
                        <span class="badge bg-{{ $order->status === 'processing' ? 'warning' : ($order->status === 'completed' ? 'success' : 'secondary') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Kho:</strong> {{ $order->picking?->warehouse?->name ?? '-' }}
                    </p>
                    @if($order->picking?->packed_at)
                        <p class="mb-0 mt-2">
                            <small class="text-muted">
                                <strong>Đóng gói:</strong> {{ $order->picking->packed_at->format('d/m/Y H:i') }}<br>
                                <strong>Người đóng gói:</strong> {{ $order->picking->packedBy?->name ?? 'N/A' }}
                            </small>
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    @if (!$order->picking || $order->picking->status === 'PENDING')
                        <button type="button" class="btn btn-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                            ✓ Chọn kho
                        </button>
                    @endif

                    @if ($order->picking && $order->picking->status === 'CONFIRMED')
                        <form action="{{ route('admin.orders.fulfillment.pack', $order->picking) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100" onclick="return confirm('Đóng gói & giao vận chuyển?')">
                                📦 Đóng gói
                            </button>
                        </form>
                    @endif

                    @if ($order->picking && $order->picking->status === 'PACKED' && $order->status === 'processing')
                        <form action="{{ route('admin.orders.fulfillment.ship', $order) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Cập nhật giao hàng thành công?')">
                                ✓ Giao hàng thành công
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.orders.fulfillment.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        ← Quay lại danh sách
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
                            <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach (\App\Models\Warehouse::where('operational_status', 'ACTIVE')->get() as $wh)
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
</div>
@endsection

@push('scripts')
<script>
// Tự động mở modal nếu có lỗi validation warehouse_id
@if($errors->has('warehouse_id'))
    document.addEventListener('DOMContentLoaded', function() {
        var warehouseModal = new bootstrap.Modal(document.getElementById('warehouseModal'));
        warehouseModal.show();
    });
@endif
</script>
@endpush
