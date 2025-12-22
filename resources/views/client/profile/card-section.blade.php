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
                    <button type="button" class="btn btn-light" style="border-radius: 10px; font-weight: 700;"
                        data-toggle="modal" data-target="#withdrawModal">
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

{{-- Modal rút tiền --}}
@include('client.profile.cards.withdraw-modal')


