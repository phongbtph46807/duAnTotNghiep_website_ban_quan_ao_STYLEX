@extends('admin.layouts.app')

@section('title', 'Lấy Hàng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">🔍 Lấy Hàng - #{{ $order->code }}</h1>
        <a href="{{ route('admin.orders.fulfillment.index') }}" class="btn btn-secondary btn-sm">
            <i class="ri-arrow-left-line"></i> Quay Lại
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Khách Hàng</small>
                        <div class="fw-bold">{{ $order->buyer_name }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Tổng Tiền</small>
                        <div class="fw-bold text-danger">{{ number_format($order->total) }} đ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Kho</small>
                        <div class="fw-bold"><span class="badge bg-info">{{ $order->picking->warehouse->name }}</span></div>
                    </div>
                    <div>
                        <small class="text-muted">Ngày Tạo</small>
                        <div class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh Sách Hàng Cần Lấy</h5>
        </div>
        <form action="{{ route('admin.orders.fulfillment.picking.store', $order) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>SKU</th>
                                <th class="text-center" style="width: 120px;">Số Lượng Cần</th>
                                <th class="text-center" style="width: 150px;">Số Lượng Đã Lấy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->variant->product->name }}</div>
                                        <small class="text-muted">{{ $item->variant->name }}</small>
                                    </td>
                                    <td><code>{{ $item->variant->sku }}</code></td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="picked_qty[{{ $item->id }}]" class="form-control form-control-sm text-center" min="0" max="{{ $item->quantity }}" value="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                    <strong><i class="ri-information-line"></i> Hướng dẫn:</strong> Nhập số lượng đã lấy cho từng sản phẩm. Tổng số lượng phải bằng số lượng cần lấy.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-line"></i> Xác Nhận Lấy Hàng
                    </button>
                    <a href="{{ route('admin.orders.fulfillment.index') }}" class="btn btn-secondary">
                        <i class="ri-close-line"></i> Hủy
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
