@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Danh sách phần thưởng vòng quay</h3>
            <a href="{{ route('admin.spin.create') }}" class="btn btn-primary">+ Thêm mới</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Loại</th>
                <th>Giá trị tham chiếu</th>
                <th>Xác suất</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>
            @foreach($spinPrizes as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->type }}</td>
                    <td>
                        @if($item->type === 'VOUCHER')
                            {{ $item->voucher?->name ?? 'Không tìm thấy voucher' }}
                        @else
                            {{ $item->value_reference }}
                        @endif
                    </td>
                    <td>{{ $item->probability }}</td>
                    <td>
                        <a href="{{ route('admin.spin.edit', $item->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('admin.spin.destroy', $item->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Xóa phần thưởng này?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
