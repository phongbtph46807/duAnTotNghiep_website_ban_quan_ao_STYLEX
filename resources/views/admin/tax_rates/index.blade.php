@extends('admin.layouts.app')

@section('content')
<div class="page-title">
    <h1>Thuế</h1>
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <nav aria-label="breadcrumb" class="breadcrumb-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thuế</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <form class="d-flex gap-2" method="GET" action="{{ route('admin.tax_rates.index') }}">
                <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Tìm theo tên thuế...">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>
            <a href="{{ route('admin.tax_rates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm mức thuế
            </a>
        </div>
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Tỷ lệ</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxRates as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ rtrim(rtrim(number_format($item->rate * 100, 2, '.', ''), '0'), '.') }}%</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.tax_rates.edit', $item) }}">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <form class="d-inline" method="POST" action="{{ route('admin.tax_rates.destroy', $item) }}"
                                onsubmit="return confirm('Xóa mức thuế này?');">
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
                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $taxRates->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection