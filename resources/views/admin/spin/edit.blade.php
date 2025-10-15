@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h3>Chỉnh sửa phần thưởng</h3>

        <form action="{{ route('admin.spin.update', $spin->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tên phần thưởng</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $spin->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại phần thưởng</label>
                <select name="type" class="form-select" id="typeSelect">
                    <option value="VOUCHER" {{ $spin->type == 'VOUCHER' ? 'selected' : '' }}>Voucher</option>
                    <option value="LOYALTY_POINTS" {{ $spin->type == 'LOYALTY_POINTS' ? 'selected' : '' }}>Điểm thưởng</option>
                    <option value="NONE" {{ $spin->type == 'NONE' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>

            <div class="mb-3" id="voucherSelect" style="display: {{ $spin->type == 'VOUCHER' ? 'block' : 'none' }}">
                <label class="form-label">Voucher</label>
                <select name="value_reference" class="form-select">
                    @foreach($vouchers as $v)
                        <option value="{{ $v->id }}" {{ $spin->value_reference == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="pointInput" style="display: {{ $spin->type == 'LOYALTY_POINTS' ? 'block' : 'none' }}">
                <label class="form-label">Giá trị điểm</label>
                <input type="number" name="value_reference" class="form-control"
                       value="{{ old('value_reference', $spin->value_reference) }}" step="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Xác suất</label>
                <input type="number" name="probability" class="form-control" step="0.0001"
                       value="{{ old('probability', $spin->probability) }}" required>
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('admin.spin.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script>
        document.getElementById('typeSelect').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('voucherSelect').style.display = (type === 'VOUCHER') ? 'block' : 'none';
            document.getElementById('pointInput').style.display = (type === 'LOYALTY_POINTS') ? 'block' : 'none';
        });
    </script>
@endsection
