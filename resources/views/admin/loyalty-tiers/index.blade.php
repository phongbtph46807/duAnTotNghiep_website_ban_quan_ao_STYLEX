@extends('admin.layouts.app')

@section('title', 'Quản lý Hạng thành viên')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách Hạng thành viên</h2>
        <a href="{{ route('admin.loyalty-tiers.create') }}" class="btn btn-primary">
            + Thêm Hạng mới
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Hạng</th>
                <th>Ngưỡng Chi tiêu (VNĐ)</th>
                <th>Giảm giá (%)</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tiers as $tier)
                <tr>
                    <td>{{ $tier->id }}</td>
                    <td>{{ $tier->name }}</td>
                    <td>{{ number_format($tier->min_spend_required, 0, ',', '.') }}</td>
                    <td>{{ number_format($tier->discount_rate, 2) }}%</td>
                    <td>
                        <a href="{{ route('admin.loyalty-tiers.edit', $tier) }}" class="btn btn-sm btn-info">Sửa</a>

                        <form action="{{ route('admin.loyalty-tiers.destroy', $tier) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa hạng {{ $tier->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
