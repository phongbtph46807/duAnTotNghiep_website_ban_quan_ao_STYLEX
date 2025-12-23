@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-0">Cảnh Báo Tồn Kho Thấp</h2>
            <small class="text-muted">Ngưỡng cảnh báo: {{ $lowStockThreshold }} sản phẩm</small>
        </div>
        <div class="col-md-4 text-end">
            @if($lowStockCount > 0)
                <span class="badge bg-danger fs-6">{{ $lowStockCount }} sản phẩm cảnh báo</span>
            @else
                <span class="badge bg-success fs-6">Tất cả sản phẩm đều đủ hàng</span>
            @endif
        </div>
    </div>

    <form method="GET" class="card p-2 mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả kho --</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="SKU hoặc tên sản phẩm..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bx bx-search"></i> Tìm
                </button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-sm">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%">SKU</th>
                        <th style="width: 35%">Sản Phẩm</th>
                        <th style="width: 15%">Màu / Size</th>
                        <th style="width: 15%" class="text-center">Tồn Kho</th>
                        <th style="width: 15%" class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variantsWithStock as $variant)
                        <tr class="{{ $variant->is_low_stock ? 'table-danger' : '' }}">
                            <td><span class="badge bg-secondary">{{ $variant->sku }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($variant->product->thumbnail)
                                        <img src="{{ asset('storage/' . $variant->product->thumbnail) }}" alt="{{ $variant->product->name }}" 
                                             class="rounded" style="width: 28px; height: 28px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; flex-shrink: 0;">
                                            <i class="bx bx-image-alt text-muted" style="font-size: 14px;"></i>
                                        </div>
                                    @endif
                                    <span class="text-truncate">{{ $variant->product->name }}</span>
                                </div>
                            </td>
                            <td>
                                <small>
                                    @if($variant->color)
                                        <span class="badge bg-info">{{ $variant->color->name }}</span>
                                    @endif
                                    @if($variant->size)
                                        <span class="badge bg-secondary">{{ $variant->size->name }}</span>
                                    @endif
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $variant->total_on_hand_stock > 10 ? 'success' : ($variant->total_on_hand_stock > 0 ? 'warning' : 'danger') }}">
                                    {{ number_format($variant->total_on_hand_stock) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.inventory.stock-in.create') }}" class="btn btn-xs btn-outline-success" title="Nhập hàng">
                                    <i class="bx bx-plus"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="bx bx-inbox"></i> Không có sản phẩm cảnh báo
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $variantsWithStock->links() }}
    </div>
</div>

<style>
    .btn-group-sm .btn { padding: 0.35rem 0.6rem; font-size: 0.8rem; }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    .table-sm { font-size: 0.875rem; }
</style>
@endsection
