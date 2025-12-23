@extends('admin.layouts.app')

@section('content')
<style>
body, .page-content {
    margin: 0;
    padding: 0;
}

.container-fluid {
    padding: 15px;
    width: 100%;
}

.row {
    margin-right: -15px;
    margin-left: -15px;
}

.form-label {
    margin-bottom: 0.5rem;
    display: inline-block;
}

.form-control {
    display: block;
    width: 100%;
}
</style>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="bx bx-check"></i> Xác Nhận QC - {{ $request->batch_number }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Sản Phẩm:</strong> {{ $request->variant->product->name }}</p>
                            <p><strong>SKU:</strong> {{ $request->variant->sku }}</p>
                            <p><strong>Giá Nhập:</strong> {{ number_format($request->cost_price, 0, ',', '.') }}đ</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Kho:</strong> {{ $request->warehouse->name }}</p>
                            <p><strong>Mã Lô:</strong> {{ $request->batch_number }}</p>
                            <p><strong>Ngày Nhập:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('admin.inventory.stock-in.confirm-qc', $request->id) }}" method="POST" id="qcForm">
                        @csrf

                        <h5 class="mb-3"><strong>Tổng số lượng: {{ $request->quantity }}</strong></h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số Lượng QC Pass <span class="text-danger">*</span></label>
                                <input type="number" name="passed_qty" id="passedQty"
                                       class="form-control @error('passed_qty') is-invalid @enderror"
                                       value="{{ old('passed_qty', 0) }}" min="0" max="{{ $request->quantity }}">
                                @error('passed_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số Lượng QC Fail <span class="text-danger">*</span></label>
                                <input type="number" name="failed_qty" id="failedQty"
                                       class="form-control @error('failed_qty') is-invalid @enderror"
                                       value="{{ old('failed_qty', 0) }}" min="0" max="{{ $request->quantity }}">
                                @error('failed_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mb-3" id="totalAlert">
                            <strong>Tổng cộng:</strong> <span id="totalQty">0</span> / {{ $request->quantity }}
                            <span id="totalStatus" class="ms-2"></span>
                        </div>

                        <div class="mb-3" id="failedHandlingSection" style="display: none;">
                            <label class="form-label"><strong>Xử Lý Hàng Không Đạt</strong> <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="failed_handling" id="damaged" value="damaged" checked>
                                <label class="form-check-label" for="damaged">
                                    Chuyển vào kho hàng hỏng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="failed_handling" id="return_supplier" value="return_supplier">
                                <label class="form-check-label" for="return_supplier">
                                    Trả hàng cho nhà cung cấp
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="defectSection" style="display: none;">
                            <label class="form-label"><strong>Đánh Giá Lỗi</strong></label>
                            <div class="mb-2">
                                <label class="form-label">Loại Lỗi</label>
                                <select name="defect_type" class="form-control" id="defectType">
                                    <option value="">-- Chọn loại lỗi --</option>
                                    <option value="SIZE_ERROR">Sai kích thước</option>
                                    <option value="COLOR_ERROR">Sai màu sắc</option>
                                    <option value="DAMAGED">Hàng bị hỏng</option>
                                    <option value="STAIN">Bị bẩn/vết</option>
                                    <option value="SEAM_DEFECT">Lỗi may</option>
                                    <option value="MATERIAL_DEFECT">Lỗi vải</option>
                                    <option value="OTHER">Khác</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Mức Độ Lỗi</label>
                                <select name="defect_level" class="form-control" id="defectLevel">
                                    <option value="LIGHT">Nhẹ</option>
                                    <option value="MEDIUM" selected>Trung bình</option>
                                    <option value="HEAVY">Nặng</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="shortageSection" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accept_shortage" id="acceptShortage">
                                <label class="form-check-label" for="acceptShortage">
                                    <strong>Chấp nhận thiếu hàng (yêu cầu ghi chú)</strong>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Ghi Chú QC</strong> <span id="notesRequired" class="text-danger" style="display: none;">*</span></label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" id="notesField">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="bx bx-check"></i> Xác Nhận QC
                            </button>
                            <a href="{{ route('admin.inventory.stock-in.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passedQty = document.getElementById('passedQty');
    const failedQty = document.getElementById('failedQty');
    const totalQty = document.getElementById('totalQty');
    const totalStatus = document.getElementById('totalStatus');
    const submitBtn = document.getElementById('submitBtn');
    const totalAlert = document.getElementById('totalAlert');
    const maxQty = {{ $request->quantity }};

    function updateTotal() {
        const passed = parseInt(passedQty.value) || 0;
        const failed = parseInt(failedQty.value) || 0;
        const total = passed + failed;

        totalQty.textContent = total;
        updateFailedUI();
        updateShortageUI();

        if (total === maxQty) {
            totalStatus.innerHTML = '<span class="badge bg-success">✓ Chính xác</span>';
            totalAlert.classList.remove('alert-warning', 'alert-danger');
            totalAlert.classList.add('alert-success');
            if (!checkShortage()) submitBtn.disabled = false;
        } else if (total < maxQty) {
            totalStatus.innerHTML = '<span class="badge bg-warning">Thiếu ' + (maxQty - total) + '</span>';
            totalAlert.classList.remove('alert-success', 'alert-danger');
            totalAlert.classList.add('alert-warning');
            submitBtn.disabled = true;
        } else {
            totalStatus.innerHTML = '<span class="badge bg-danger">Vượt ' + (total - maxQty) + '</span>';
            totalAlert.classList.remove('alert-success', 'alert-warning');
            totalAlert.classList.add('alert-danger');
            submitBtn.disabled = true;
        }
    }

    const acceptShortage = document.getElementById('acceptShortage');
    const shortageSection = document.getElementById('shortageSection');
    const notesRequired = document.getElementById('notesRequired');
    const notesField = document.getElementById('notesField');

    function updateFailedUI() {
        const failed = parseInt(failedQty.value) || 0;
        const failedHandlingSection = document.getElementById('failedHandlingSection');
        const defectSection = document.getElementById('defectSection');
        
        if (failed > 0) {
            failedHandlingSection.style.display = 'block';
            defectSection.style.display = 'block';
        } else {
            failedHandlingSection.style.display = 'none';
            defectSection.style.display = 'none';
        }
    }

    passedQty.addEventListener('input', updateTotal);
    failedQty.addEventListener('input', updateTotal);

    function checkShortage() {
        const passed = parseInt(passedQty.value) || 0;
        const failed = parseInt(failedQty.value) || 0;
        const total = passed + failed;
        return total < maxQty;
    }

    function updateShortageUI() {
        if (checkShortage()) {
            shortageSection.style.display = 'block';
            notesRequired.style.display = 'inline';
            acceptShortage.checked = false;
            submitBtn.disabled = true;
        } else {
            shortageSection.style.display = 'none';
            notesRequired.style.display = 'none';
            acceptShortage.checked = false;
            submitBtn.disabled = false;
        }
    }

    acceptShortage.addEventListener('change', function() {
        if (this.checked) {
            if (!notesField.value.trim()) {
                alert('Vui lòng nhập ghi chú khi chấp nhận thiếu hàng');
                this.checked = false;
                return;
            }
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });

    notesField.addEventListener('input', function() {
        if (acceptShortage.checked && !this.value.trim()) {
            submitBtn.disabled = true;
        } else if (acceptShortage.checked) {
            submitBtn.disabled = false;
        }
    });

    document.getElementById('qcForm').addEventListener('submit', function(e) {
        if (checkShortage() && !acceptShortage.checked) {
            e.preventDefault();
            alert('Vui lòng chấp nhận thiếu hàng hoặc nhập đủ số lượng');
            return false;
        }
    });

    updateTotal();
    updateShortageUI();

    passedQty.addEventListener('input', updateShortageUI);
    failedQty.addEventListener('input', updateShortageUI);
});
</script>
@endsection
