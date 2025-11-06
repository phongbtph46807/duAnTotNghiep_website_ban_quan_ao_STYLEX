<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn đơn hàng {{ $d['order_code'] ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#222; margin:0; padding:24px; background:#f7f7f9; }
        .container { max-width:760px; margin:0 auto; background:#fff; border:1px solid #eee; border-radius:8px; overflow:hidden; }
        .header { padding:20px 24px; background:#111827; color:#fff; }
        .header h1 { margin:0; font-size:20px; }
        .sub { color:#d1d5db; font-size:13px; margin-top:4px; }
        .section { padding:20px 24px; border-bottom:1px solid #f0f0f0; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        .label { color:#6b7280; font-size:12px; text-transform:uppercase; letter-spacing:.05em; }
        .value { font-size:14px; margin-top:6px; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid #f3f4f6; font-size:14px; }
        th { color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.05em; }
        .right { text-align:right; }
        .muted { color:#6b7280; }
        .total-row td { border-top:2px solid #e5e7eb; font-weight:700; }
        .footer { padding:16px 24px; font-size:12px; color:#6b7280; }
        .brand { font-weight:700; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Hóa đơn đơn hàng {{ $d['order_code'] ?? '' }}</h1>
        <div class="sub">Cảm ơn bạn đã mua sắm tại STYLEX</div>
    </div>

    <div class="section grid">
        <div>
            <div class="label">Khách hàng</div>
            <div class="value">
                {{ $d['full_name'] }}<br>
                {{ $d['phone'] }}<br>
                @if(!empty($d['email'])) {{ $d['email'] }}<br>@endif
            </div>
        </div>
        <div>
            <div class="label">Giao hàng</div>
            <div class="value">
                {{ $d['address'] }}<br>
                {{ $d['city'] }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="label">Thông tin đơn</div>
        <div class="grid" style="margin-top:8px;">
            <div class="value">Mã đơn: <strong>{{ $d['order_code'] }}</strong></div>
            <div class="value right">Ngày đặt: {{ $d['placed_at'] }}</div>
        </div>
        <div class="grid" style="margin-top:6px;">
            <div class="value">Thanh toán: {{ strtoupper($d['payment_method']) }} ({{ $d['payment_status'] }})</div>
            <div class="value right">Trạng thái: {{ ucfirst($d['status']) }}</div>
        </div>
        @if(!empty($d['note']))
            <div class="value" style="margin-top:8px;">
                Ghi chú: <span class="muted">{{ $d['note'] }}</span>
            </div>
        @endif
    </div>

    <div class="section">
        <table>
            <thead>
            <tr>
                <th>Sản phẩm</th>
                <th class="right">SL</th>
                <th class="right">Đơn giá</th>
                <th class="right">Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            @foreach($d['items'] as $row)
                <tr>
                    <td>
                        <div>{{ $row['product_name'] }}</div>
                        @if(!empty($row['variant_label']))
                            <div class="muted" style="font-size:12px;">{{ $row['variant_label'] }}</div>
                        @endif
                    </td>
                    <td class="right">{{ $row['quantity'] }}</td>
                    <td class="right">{{ number_format($row['unit_price']) }} đ</td>
                    <td class="right">{{ number_format($row['line_total']) }} đ</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="right muted">Tạm tính</td>
                <td class="right">{{ number_format($d['subtotal']) }} đ</td>
            </tr>
            <tr>
                <td colspan="3" class="right muted">Phí vận chuyển</td>
                <td class="right">{{ number_format($d['shipping_fee']) }} đ</td>
            </tr>
            @if(!empty($d['discount']) && (int)$d['discount'] > 0)
            <tr>
                <td colspan="3" class="right muted">Giảm giá</td>
                <td class="right">-{{ number_format($d['discount']) }} đ</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" class="right">Tổng cộng</td>
                <td class="right">{{ number_format($d['total']) }} đ</td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        Một lần nữa cảm ơn bạn đã mua sắm tại <span class="brand">STYLEX</span>.<br>
        Email này được gửi tự động, vui lòng không trả lời trực tiếp.
    </div>
</div>
</body>
</html>


