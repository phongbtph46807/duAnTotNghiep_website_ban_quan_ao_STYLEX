@extends('admin.layouts.app')

@section('title', 'Thêm lương')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm/Sửa lương nhân viên</h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nhân viên</label>
                        <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach ($employees  as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tháng</label>
                        <select name="month" class="form-control @error('month') is-invalid @enderror" required>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', $month) == $m ? 'selected' : '' }}>
                                    Tháng {{ $m }}
                                </option>
                            @endfor
                        </select>
                        @error('month') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Năm</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year', $year) }}" required>
                        @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Lương cơ bản (VND)</label>
                        <input type="number" id="base_salary" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror"
                               value="{{ old('base_salary') }}" required onchange="calculateDeductions()">
                        @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Thưởng (VND)</label>
                        <input type="number" id="bonus" name="bonus" class="form-control @error('bonus') is-invalid @enderror"
                               value="{{ old('bonus', 0) }}" onchange="calculateDeductions()">
                        @error('bonus') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Khấu trừ tự động <small class="text-muted">(BHXH + Thuế)</small></label>
                        <input type="number" id="deduction" name="deduction" class="form-control @error('deduction') is-invalid @enderror"
                               value="{{ old('deduction', 0) }}" readonly>
                        @error('deduction') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Chi tiết khấu trừ -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-3">📊 Chi tiết Khấu trừ</h6>
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <small class="text-muted">BHXH (8%)</small>
                                        <div id="bhxh_amount" class="fw-bold text-info">0 VND</div>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">BHYT (1.5%)</small>
                                        <div id="bhyt_amount" class="fw-bold text-info">0 VND</div>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">BHTN (1%)</small>
                                        <div id="bhtn_amount" class="fw-bold text-info">0 VND</div>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Thuế TNCN</small>
                                        <div id="tax_amount" class="fw-bold text-warning">0 VND</div>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Tổng khấu trừ</small>
                                        <div id="total_deduction" class="fw-bold text-danger">0 VND</div>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Thực nhận</small>
                                        <div id="net_salary" class="fw-bold text-success">0 VND</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lưu lương</button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="button" class="btn btn-info" onclick="calculateDeductions()">🔄 Tính lại khấu trừ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateDeductions() {
    const baseSalary = parseFloat(document.getElementById('base_salary').value) || 0;
    const bonus = parseFloat(document.getElementById('bonus').value) || 0;
    const totalIncome = baseSalary + bonus;

    // Các tỷ lệ khấu trừ theo quy định Việt Nam
    const BHXH_RATE = 0.08;  // 8%
    const BHYT_RATE = 0.015; // 1.5%
    const BHTN_RATE = 0.01;  // 1%
    const PERSONAL_DEDUCTION = 11000000; // 11 triệu VND giảm trừ bản thân

    // Tính BHXH, BHYT, BHTN (chỉ tính trên lương cơ bản, không tính thưởng)
    const bhxh = baseSalary * BHXH_RATE;
    const bhyt = baseSalary * BHYT_RATE;
    const bhtn = baseSalary * BHTN_RATE;
    const socialInsurance = bhxh + bhyt + bhtn;

    // Tính thuế thu nhập cá nhân
    const taxableIncome = Math.max(0, totalIncome - socialInsurance - PERSONAL_DEDUCTION);
    let personalTax = 0;

    // Bậc thuế luũy tiến theo quy định Việt Nam
    if (taxableIncome <= 5000000) {
        personalTax = taxableIncome * 0.05;
    } else if (taxableIncome <= 10000000) {
        personalTax = 5000000 * 0.05 + (taxableIncome - 5000000) * 0.10;
    } else if (taxableIncome <= 18000000) {
        personalTax = 5000000 * 0.05 + 5000000 * 0.10 + (taxableIncome - 10000000) * 0.15;
    } else if (taxableIncome <= 32000000) {
        personalTax = 5000000 * 0.05 + 5000000 * 0.10 + 8000000 * 0.15 + (taxableIncome - 18000000) * 0.20;
    } else {
        personalTax = 5000000 * 0.05 + 5000000 * 0.10 + 8000000 * 0.15 + 14000000 * 0.20 + (taxableIncome - 32000000) * 0.25;
    }

    const totalDeduction = socialInsurance + personalTax;
    const netSalary = totalIncome - totalDeduction;

    // Cập nhật giao diện
    document.getElementById('bhxh_amount').textContent = formatCurrency(bhxh);
    document.getElementById('bhyt_amount').textContent = formatCurrency(bhyt);
    document.getElementById('bhtn_amount').textContent = formatCurrency(bhtn);
    document.getElementById('tax_amount').textContent = formatCurrency(personalTax);
    document.getElementById('total_deduction').textContent = formatCurrency(totalDeduction);
    document.getElementById('net_salary').textContent = formatCurrency(netSalary);
    document.getElementById('deduction').value = Math.round(totalDeduction);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' VND';
}

// Tính toán khi tải trang
document.addEventListener('DOMContentLoaded', function() {
    calculateDeductions();
});
</script>
@endsection
