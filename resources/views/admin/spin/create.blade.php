@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h3>Thêm phần thưởng vòng quay</h3>

        <form action="{{ route('admin.spin.store') }}" method="POST" class="mt-3">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên phần thưởng</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Loại phần thưởng</label>
                <select name="type" class="form-select" required id="typeSelect">
                    <option value="">-- Chọn loại --</option>
                    <option value="VOUCHER">Voucher</option>
                    <option value="LOYALTY_POINTS">Điểm thưởng</option>
                    <option value="NONE">Khác</option>
                </select>
            </div>

            <div class="mb-3" id="voucherSelect" style="display:none;">
                <label class="form-label">Voucher</label>
                <select name="value_reference" class="form-select">
                    <option value="">-- Chọn voucher --</option>
                    @foreach($vouchers as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="pointInput" style="display:none;">
                <label class="form-label">Giá trị điểm</label>
                <input type="number" name="value_reference" class="form-control" step="1" placeholder="Nhập điểm thưởng">
            </div>

            <div class="mb-3">
                <label class="form-label">Xác suất (0.0000 - 1.0000)</label>
                <input type="number" name="probability" class="form-control" step="0.0001" required>
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.spin.index') }}" class="btn btn-secondary">Hủy</a>
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
