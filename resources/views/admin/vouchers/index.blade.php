@extends('admin.layouts.app')

@section('title', 'Quản lý voucher')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý voucher</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Khuyến mãi</a></li>
                    <li class="breadcrumb-item">Voucher</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="col-12 d-flex justify-content-end mb-3">
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Tạo voucher</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="col-12">
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Giới hạn</th>
                        <th>Đã dùng</th>
                        <th>Trạng thái</th>
                        <th>Hiệu lực</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                        <tr>
                            <td><strong>{{ $voucher->code }}</strong></td>
                            <td>{{ $voucher->type === 'percent' ? 'Phần trăm' : 'Cố định' }}</td>
                            <td>
                                @if($voucher->type === 'percent')
                                    {{ rtrim(rtrim(number_format($voucher->value, 2), '0'), '.') }}%
                                @else
                                    {{ number_format($voucher->value, 0, ',', '.') }} ₫
                                @endif
                            </td>
                            <td>{{ $voucher->usage_limit ?? '—' }}</td>
                            <td>{{ $voucher->used_count }}</td>
                            <td>
                                <span class="badge {{ $voucher->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $voucher->is_active ? 'Kích hoạt' : 'Tắt' }}</span>
                            </td>
                            <td>
                                @if($voucher->starts_at) {{ $voucher->starts_at->format('d/m/Y') }} @endif
                                -
                                @if($voucher->ends_at) {{ $voucher->ends_at->format('d/m/Y') }} @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa voucher này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center p-4">Chưa có voucher</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $vouchers->links() }}</div>
    </div>
    </div>
@endsection


