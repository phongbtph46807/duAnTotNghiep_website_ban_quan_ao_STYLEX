<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; margin-top: 150px;">
            <div class="modal-header" style="background:#f8fafc;">
                <h5 class="modal-title" id="withdrawModalLabel" style="font-weight: 800;">
                    <i class="ri-wallet-3-line me-2" style="color:#6777ef;"></i>Yêu cầu rút tiền
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('client.profile.withdraw') }}" method="POST" id="withdrawForm">
                @csrf

                <div class="modal-body p-4">
                    <div class="alert alert-info" style="border-radius: 12px;">
                        Số dư khả dụng:
                        <b id="availableBalanceText">{{ number_format((int) ($user->wallet_balance ?? 0)) }} ₫</b>
                        <input type="hidden" id="availableBalance" value="{{ (int) ($user->wallet_balance ?? 0) }}">
                    </div>

                    <div id="withdrawError" class="alert alert-danger d-none" style="border-radius: 12px;"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 700;">Ngân hàng</label>
                            <select name="bank_code" id="bankSelect"
                                    class="form-control @error('bank_code') is-invalid @enderror"
                                    style="width:100%; border-radius: 10px;">
                                <option value="">-- Chọn ngân hàng --</option>
                                <option value="VCB">Vietcombank (VCB)</option>
                                <option value="TCB">Techcombank (TCB)</option>
                                <option value="BIDV">BIDV</option>
                                <option value="VTB">VietinBank</option>
                                <option value="ACB">ACB</option>
                                <option value="MBB">MB Bank</option>
                                <option value="VPB">VPBank</option>
                                <option value="TPB">TPBank</option>
                                <option value="STB">Sacombank</option>
                                <option value="VIB">VIB</option>
                                <option value="SHB">SHB</option>
                                <option value="HDB">HDBank</option>
                                <option value="OCB">OCB</option>
                                <option value="EIB">Eximbank</option>
                                <option value="SCB">SCB</option>
                            </select>
                            @error('bank_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 700;">Số thẻ / Số tài khoản</label>
                            <input type="text" name="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   placeholder="Số tài khoản ngân hàng" value="{{ old('account_number') }}"
                                   style="border-radius: 10px;">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 700;">Chủ tài khoản</label>
                            <input type="text" name="account_name"
                                   class="form-control @error('account_name') is-invalid @enderror"
                                   placeholder="Tên người dùng tài khoản" value="{{ old('account_name') }}"
                                   style="border-radius: 10px;">
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 700;">Số tiền cần rút</label>
                            <input type="text" id="amountDisplay" class="form-control" placeholder="VD: 100,000"
                                   style="border-radius: 10px; font-weight: 800;">
                            <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                            @error('amount')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                            <div class="text-muted mt-1" style="font-size: 12px;">
                                Tối đa: <b>{{ number_format((int) ($user->wallet_balance ?? 0)) }} ₫</b>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="font-weight: 700;">Ghi chú (tuỳ chọn)</label>
                            <textarea name="note" rows="2" class="form-control @error('note') is-invalid @enderror"
                                      placeholder="VD: Rút tiền về tài khoản cá nhân" style="border-radius: 10px;">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background:#fff;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">
                        Đóng
                    </button>
                    <button type="submit" id="btnWithdrawSubmit" class="btn btn-primary"
                            style="border-radius: 10px; font-weight: 800;">
                        <i class="ri-send-plane-2-line me-1"></i>Rút tiền
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountDisplay = document.getElementById('amountDisplay');
    const amountHidden  = document.getElementById('amount');
    const balanceEl     = document.getElementById('availableBalance');
    const errBox        = document.getElementById('withdrawError');
    const form          = document.getElementById('withdrawForm');
    const btnSubmit     = document.getElementById('btnWithdrawSubmit');
    const bankSelectEl  = document.getElementById('bankSelect');
    const modalEl       = document.getElementById('withdrawModal');

    if (!amountDisplay || !amountHidden || !balanceEl || !errBox || !form || !btnSubmit || !bankSelectEl) {
        console.error('Không tìm thấy các phần tử cần thiết');
        return;
    }

    const MAX = parseInt(balanceEl.value || '0', 10);
    const MIN = 100000; // Tối thiểu 100,000 ₫ (theo server validation)

    const onlyDigits = (str) => (str || '').toString().replace(/[^\d]/g, '');
    const formatVND  = (nStr) => nStr ? nStr.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '';

    function setError(msg) {
        if (!msg) {
            errBox.classList.add('d-none');
            errBox.textContent = '';
            return;
        }
        errBox.textContent = msg;
        errBox.classList.remove('d-none');
    }

    function getAmount() {
        return parseInt(amountHidden.value || '0', 10);
    }

    function getBank() {
        return (bankSelectEl.value || '').trim();
    }

    // Chỉ hiển thị cảnh báo, không chặn submit
    function showWarning() {
        const bank = getBank();
        const amount = getAmount();

        if (!bank) {
            setError('Vui lòng chọn ngân hàng.');
            return;
        }
        if (!amount || amount <= 0) {
            setError('Vui lòng nhập số tiền cần rút.');
            return;
        }
        if (amount < MIN) {
            setError('Số tiền rút tối thiểu là 100.000 ₫.');
            return;
        }
        if (amount > MAX) {
            setError('Số tiền rút vượt quá số dư khả dụng.');
            return;
        }

        setError('');
    }

    // Format số tiền khi nhập
    bankSelectEl.addEventListener('change', showWarning);

    amountDisplay.addEventListener('input', function() {
        const raw = onlyDigits(amountDisplay.value);
        const n = parseInt(raw || '0', 10);
        amountHidden.value = n > 0 ? n : '';
        amountDisplay.value = raw ? formatVND(raw) : '';
        showWarning();
    });

    // KHÔNG chặn submit - để form submit bình thường
    form.addEventListener('submit', function(e) {
        showWarning();
        // Không có e.preventDefault() - form sẽ submit
        console.log('Form đang submit...');
    });

    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function() {
            btnSubmit.disabled = false;
            showWarning();
        });
        modalEl.addEventListener('hidden.bs.modal', function() {
            setError('');
            form.reset();
            amountDisplay.value = '';
            amountHidden.value = '';
        });
    }

    // Đảm bảo nút luôn enabled
    btnSubmit.disabled = false;
    showWarning();
});
</script>

