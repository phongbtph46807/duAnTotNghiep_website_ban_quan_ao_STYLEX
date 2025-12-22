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
                        <th>Địa chỉ</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Kho</th>
                        <th>Sản phẩm (SKU/GTIN)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shippingOrders as $order)
                        @php
                            $pickingStatus = $order->picking?->status ?? 'PENDING';
                            $status = match(true) {
                                $order->status === 'delivered' => 'SHIPPED',
                                $order->status === 'shipping' => 'PACKED',
                                default => $pickingStatus
                            };
                            $statusClass = match($status) {
                                'PENDING' => 'badge bg-secondary',
                                'CONFIRMED' => 'badge bg-info',
                                'PICKING' => 'badge bg-warning',
                                'PACKED' => 'badge bg-primary',
                                'SHIPPED' => 'badge bg-success',
                                'CANCELLED' => 'badge bg-danger',
                                default => 'badge bg-light'
                            };
                            $statusText = match($status) {
                                'PENDING' => '⏳ Chờ chọn kho',
                                'CONFIRMED' => '✓ Đã xác nhận',
                                'PICKING' => '📋 Đang lấy hàng',
                                'PACKED' => '📦 Đã đóng gói',
                                'SHIPPED' => '✓ Đã giao',
                                'CANCELLED' => '❌ Đã hủy',
                                default => 'Không xác định'
                            };
                        @endphp
                        <tr>
                            <td><strong>#{{ $order->code }}</strong></td>
                            <td>
                                <div>{{ $order->buyer_name ?? $order->full_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $order->buyer_phone ?? $order->phone ?? '-' }}</small>
                            </td>
                            <td><small>{{ $order->address }}</small></td>
                            <td><strong class="text-danger">{{ number_format($order->total) }} đ</strong></td>
                            <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
                            <td><small class="badge bg-light text-dark">{{ $order->picking?->warehouse?->name ?? '-' }}</small></td>
                            <td>
                                <small>
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->variant?->product?->name ?? 'N/A' }} - Size: {{ $item->variant?->size?->name ?? '-' }} | Màu: {{ $item->variant?->color?->name ?? '-' }}</div>
                                        <div class="text-muted">SKU: {{ $item->variant?->sku ?? '-' }} | SL: {{ $item->quantity }}</div>
                                    @endforeach
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.orders.fulfillment.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-eye-line"></i>
                                    </a>

                                    @if ($status === 'PENDING')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#warehouseModal{{ $order->id }}">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    @elseif ($status === 'CONFIRMED')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#packingModal{{ $order->id }}">
                                            <i class="ri-box-3-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal chọn kho -->
                        <div class="modal fade" id="warehouseModal{{ $order->id }}" tabindex="-1" @if ($errors->has('warehouse_id') && old('order_id') == $order->id) data-bs-backdrop="static" @endif>
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chọn kho xuất - #{{ $order->code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" @if ($errors->has('warehouse_id') && old('order_id') == $order->id) disabled @endif></button>
                                    </div>
                                    <form action="{{ route('admin.orders.fulfillment.confirm', $order) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Chọn kho <span class="text-danger">*</span></label>
                                                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                                    <option value="">-- Chọn kho --</option>
                                                    @foreach ($warehouses as $wh)
                                                        <option value="{{ $wh->id }}" @if (old('warehouse_id') == $wh->id) selected @endif>{{ $wh->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('warehouse_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @if ($errors->has('warehouse_id') && old('order_id') == $order->id) disabled @endif>Hủy</button>
                                            <button type="submit" class="btn btn-success">Xác nhận</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($errors->has('warehouse_id') && old('order_id') == $order->id)
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const modal = new bootstrap.Modal(document.getElementById('warehouseModal{{ $order->id }}'));
                                    modal.show();
                                });
                            </script>
                        @endif

                        @if ($order->picking)
                        <!-- Modal xác nhận đóng gói -->
                        <div class="modal fade" id="packingModal{{ $order->id }}" tabindex="-1" data-bs-backdrop="static">
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
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Không có đơn hàng nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $shippingOrders->links() }}
    </div>
</div>
@endsection
