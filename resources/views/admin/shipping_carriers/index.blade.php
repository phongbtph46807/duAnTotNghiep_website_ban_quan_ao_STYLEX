@extends('admin.layouts.app')

@section('content')
<div class="page-title">
    <h1>Hãng vận chuyển</h1>
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <nav aria-label="breadcrumb" class="breadcrumb-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hãng vận chuyển</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <form class="d-flex gap-2" method="GET" action="{{ route('admin.shipping_carriers.index') }}">
                <input type="text" class="form-control" name="q" value="{{ $q }}"
                    placeholder="Tìm theo tên hãng...">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>
            <a href="{{ route('admin.shipping_carriers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm hãng vận chuyển
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên hãng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carriers as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-warning"
                                href="{{ route('admin.shipping_carriers.edit', $item) }}">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <form class="d-inline" method="POST"
                                action="{{ route('admin.shipping_carriers.destroy', $item) }}"
                                onsubmit="return confirm('Xóa hãng vận chuyển này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $carriers->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection