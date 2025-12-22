@extends('client.layouts.app')

@section('title', 'Ví cá nhân - ' . env('APP_NAME'))

@section('content')
    @include('client.partials.profile-styles')

    <div class="container p-t-40 p-b-60">
        <div class="row">

            <!-- Main Content -->
            <div class="col-lg-12 col-md-12">
                <div class="card profile-card">
                    <div class="profile-card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0" style="font-weight: 600; color: #333;">
                            <i class="ri-wallet-3-line me-2" style="color: #6777ef;"></i>Ví của tôi
                        </h4>
                    </div>

                    <div class="profile-card-body">

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Balance Card --}}
                        <div class="p-4 mb-4"
                            style="border-radius: 16px; background: linear-gradient(135deg, #6777ef, #3b82f6); color: #fff;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <div style="opacity:.9; font-weight: 600;">Số dư ví hiện tại</div>
                                    <div style="font-size: 34px; font-weight: 800; letter-spacing: .2px;">
                                        {{ number_format((int) ($user->wallet_balance ?? 0)) }} ₫
                                    </div>
                                    <div style="opacity:.85; font-size: 13px;">
                                        Tiền hoàn sẽ được cộng vào ví.
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    {{-- Nếu bạn chưa làm chức năng rút/nạp thì cứ để disabled cho đỡ “ảo tưởng sức mạnh” --}}
                                    <button class="btn btn-light" style="border-radius: 10px; font-weight: 700;"
                                        data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                        <i class="ri-upload-2-line me-1"></i>Rút tiền
                                    </button>

                                </div>
                            </div>
                        </div>

                        {{-- History --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0" style="font-weight: 700; color:#333;">
                                <i class="ri-history-line me-2" style="color: #6777ef;"></i>Lịch sử giao dịch
                            </h5>
                            <div class="text-muted" style="font-size: 13px;">
                                Tổng: {{ is_array($walletHistory ?? null) ? count($walletHistory) : 0 }} giao dịch
                            </div>
                        </div>

                        @if (empty($walletHistory))
                            <div class="alert alert-info mb-0">
                                Chưa có giao dịch nào trong ví.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead style="background:#f8fafc;">
                                        <tr>
                                            <th style="min-width: 160px;">Thời gian</th>
                                            <th style="min-width: 120px;">Loại</th>
                                            <th style="min-width: 140px;">Số tiền</th>
                                            <th style="min-width: 160px;">Mã đơn</th>
                                            <th>Ghi chú</th>
                                            <th style="min-width: 170px;">Số dư sau</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($walletHistory as $tx)
                                            @php
                                                $type = $tx['type'] ?? 'refund';
                                                $isRefund = $type === 'refund';
                                                $typeLabel = $isRefund ? 'Hoàn tiền' : 'Rút tiền';
                                                $sign = $isRefund ? '+' : '-';

                                                $amount = (int) ($tx['amount'] ?? 0);
                                                $balanceAfter = (int) ($tx['balance_after'] ?? 0);

                                                $badgeStyle = $isRefund
                                                    ? 'background:#e9f7ef;color:#13795b;border:1px solid #cfe9dc;'
                                                    : 'background:#fdecec;color:#b42318;border:1px solid #f8caca;';

                                                $moneyStyle = $isRefund
                                                    ? 'color:#13795b;font-weight:800;'
                                                    : 'color:#b42318;font-weight:800;';
                                            @endphp

                                            <tr>
                                                <td class="text-muted" style="font-size: 13px;">
                                                    {{ $tx['created_at'] ?? '-' }}
                                                </td>

                                                <td>
                                                    <span class="px-3 py-2"
                                                        style="border-radius: 999px; font-weight: 800; font-size: 13px; {{ $badgeStyle }}">
                                                        {{ $typeLabel }}
                                                    </span>
                                                </td>

                                                <td style="{{ $moneyStyle }}">
                                                    {{ $sign }}{{ number_format($amount) }} ₫
                                                </td>

                                                <td>
                                                    @if (!empty($tx['order_id']))
                                                        
                                                        <span style="font-weight: 700;">
                                                            {{ $tx['order_code'] ?? '#' . $tx['order_id'] }}
                                                        </span>
                                                        
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div style="font-weight: 600; color:#333;">
                                                        {{ $tx['note'] ?? '-' }}
                                                    </div>
                                                    <div class="text-muted" style="font-size: 12px;">
                                                        Bởi:
                                                        {{ $tx['created_by_name'] ?? 'User#' . ($tx['created_by'] ?? '') }}
                                                        @if (isset($tx['balance_before']))
                                                            • Trước: {{ number_format((int) $tx['balance_before']) }} ₫
                                                        @endif
                                                    </div>
                                                </td>

                                                <td style="font-weight:800;">
                                                    {{ number_format($balanceAfter) }} ₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;margin-top: 150px;">
                <div class="modal-header" style="background:#f8fafc;">
                    <h5 class="modal-title" id="withdrawModalLabel" style="font-weight: 800;">
                        <i class="ri-wallet-3-line me-2" style="color:#6777ef;"></i>Yêu cầu
                        rút tiền
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('client.profile.withdraw') }}" method="POST" id="withdrawForm">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="alert alert-info" style="border-radius: 12px;">
                            Số dư khả dụng: <b
                                id="availableBalanceText">{{ number_format((int) ($user->wallet_balance ?? 0)) }}
                                ₫</b>
                            <input type="hidden" id="availableBalance" value="{{ (int) ($user->wallet_balance ?? 0) }}">
                        </div>

                        <div id="withdrawError" class="alert alert-danger d-none" style="border-radius: 12px;">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 700;">Ngân
                                    hàng</label>

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
                                    <div class="invalid-feedback d-block">{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 700;">Số thẻ /
                                    Số tài khoản</label>
                                <input type="text" name="account_number"
                                    class="form-control @error('account_number') is-invalid @enderror"
                                    placeholder="Số tài khoản ngân hàng" value="{{ old('account_number') }}"
                                    style="border-radius: 10px;">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 700;">Chủ
                                    tài
                                    khoản</label>
                                <input type="text" name="account_name"
                                    class="form-control @error('account_name') is-invalid @enderror"
                                    placeholder="Tên người dùng tài khoản" value="{{ old('account_name') }}"
                                    style="border-radius: 10px;">
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 700;">Số
                                    tiền
                                    cần rút</label>

                                
                                <input type="text" id="amountDisplay" class="form-control" placeholder="VD: 100,000"
                                    style="border-radius: 10px; font-weight: 800;">

                                
                                <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">

                                @error('amount')
                                    <div class="text-danger mt-1" style="font-size: 13px;">
                                        {{ $message }}</div>
                                @enderror

                                <div class="text-muted mt-1" style="font-size: 12px;">
                                    Tối đa:
                                    <b>{{ number_format((int) ($user->wallet_balance ?? 0)) }}
                                        ₫</b>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" style="font-weight: 700;">Ghi
                                    chú (tuỳ chọn)</label>
                                <textarea name="note" rows="2" class="form-control @error('note') is-invalid @enderror"
                                    placeholder="VD: Rút tiền về tài khoản cá nhân" style="border-radius: 10px;">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <div class="modal-footer" style="background:#fff;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="border-radius: 10px;">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (!preview) return;

                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.src = e.target.result;
                        img.className = 'avatar-preview-large';
                        img.onclick = function() {
                            document.getElementById('avatar')?.click();
                        };
                        preview.parentNode.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        (function() {
            const amountDisplay = document.getElementById('amountDisplay');
            const amountHidden = document.getElementById('amount');
            const balanceEl = document.getElementById('availableBalance');
            const errBox = document.getElementById('withdrawError');
            const form = document.getElementById('withdrawForm');
            const btnSubmit = document.getElementById('btnWithdrawSubmit');
            const bankSelectEl = document.getElementById('bankSelect');
            const modalEl = document.getElementById('withdrawModal');

            if (!amountDisplay || !amountHidden || !balanceEl || !errBox || !form || !btnSubmit || !bankSelectEl) {
                console.error('Không tìm thấy các phần tử cần thiết');
                return;
            }

            const MAX = parseInt(balanceEl.value || '0', 10);
            const MIN = 100000; // Tối thiểu 100,000 ₫ (theo server validation)

            function onlyDigits(str) {
                return (str || '').toString().replace(/[^\d]/g, '');
            }

            function formatVND(nStr) {
                if (!nStr) return '';
                return nStr.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

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
        })();
    </script>

@endsection
