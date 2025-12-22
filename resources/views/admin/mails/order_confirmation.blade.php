<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng - {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f9f9f9; padding:20px; margin:0;">

    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; padding:30px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align:center; margin-bottom:30px;">
            <h1 style="color:#333; margin:0; font-size:24px;">{{ config('app.name') }}</h1>
            <p style="color:#666; margin:10px 0 0; font-size:14px;">Xác nhận đơn hàng</p>
        </div>

        <!-- Greeting -->
        <h2 style="color:#333; font-size:20px; margin-bottom:20px;">
            Xin chào {{ $order->full_name }}!
        </h2>

        <p style="font-size:15px; color:#444; line-height:1.6; margin-bottom:20px;">
            Cảm ơn bạn đã đặt hàng tại <strong>{{ config('app.name') }}</strong>. 
            Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý.
        </p>

        <!-- Order Info -->
        <div style="background:#f8f9fa; border-radius:6px; padding:20px; margin:20px 0;">
            <h3 style="color:#333; font-size:18px; margin:0 0 15px;">Thông tin đơn hàng</h3>
            
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Mã đơn hàng:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; font-weight:600; text-align:right;">
                        #{{ $order->code }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Ngày đặt:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; text-align:right;">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Phương thức thanh toán:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; text-align:right;">
                        {{ $order->payment_method === 'online' ? 'Thanh toán online' : 'Thanh toán khi nhận hàng (COD)' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Trạng thái:</td>
                    <td style="padding:8px 0; color:#28a745; font-size:14px; font-weight:600; text-align:right;">
                        {{ $order->status === 'pending' ? 'Chờ xác nhận' : ucfirst($order->status) }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Shipping Info -->
        <div style="background:#f8f9fa; border-radius:6px; padding:20px; margin:20px 0;">
            <h3 style="color:#333; font-size:18px; margin:0 0 15px;">Thông tin giao hàng</h3>
            
            <p style="margin:5px 0; color:#444; font-size:14px;">
                <strong>Người nhận:</strong> {{ $order->full_name }}
            </p>
            <p style="margin:5px 0; color:#444; font-size:14px;">
                <strong>Điện thoại:</strong> {{ $order->phone }}
            </p>
            <p style="margin:5px 0; color:#444; font-size:14px;">
                <strong>Email:</strong> {{ $order->email }}
            </p>
            <p style="margin:5px 0; color:#444; font-size:14px;">
                <strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}
            </p>
        </div>

        <!-- Order Items -->
        <div style="margin:20px 0;">
            <h3 style="color:#333; font-size:18px; margin:0 0 15px;">Chi tiết sản phẩm</h3>
            
            <table style="width:100%; border-collapse:collapse; border:1px solid #e0e0e0;">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th style="padding:12px; text-align:left; border-bottom:1px solid #e0e0e0; color:#666; font-size:13px;">Sản phẩm</th>
                        <th style="padding:12px; text-align:center; border-bottom:1px solid #e0e0e0; color:#666; font-size:13px;">SL</th>
                        <th style="padding:12px; text-align:right; border-bottom:1px solid #e0e0e0; color:#666; font-size:13px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding:12px; border-bottom:1px solid #e0e0e0;">
                            <div style="font-weight:600; color:#333; font-size:14px;">{{ $item->product->name }}</div>
                            @if($item->variant)
                                <div style="color:#666; font-size:12px; margin-top:4px;">
                                    @if($item->variant->size) Kích thước: {{ $item->variant->size->name }} @endif
                                    @if($item->variant->color) | Màu: {{ $item->variant->color->name }} @endif
                                    @if($item->variant->texture) | Chất liệu: {{ $item->variant->texture->name }} @endif
                                </div>
                            @endif
                        </td>
                        <td style="padding:12px; text-align:center; border-bottom:1px solid #e0e0e0; color:#333; font-size:14px;">
                            {{ $item->quantity }}
                        </td>
                        <td style="padding:12px; text-align:right; border-bottom:1px solid #e0e0e0; color:#333; font-size:14px; font-weight:600;">
                            {{ number_format($item->line_total) }} ₫
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div style="background:#f8f9fa; border-radius:6px; padding:20px; margin:20px 0;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Tạm tính:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; text-align:right;">
                        {{ number_format($order->subtotal) }} ₫
                    </td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Giảm giá:</td>
                    <td style="padding:8px 0; color:#28a745; font-size:14px; text-align:right;">
                        -{{ number_format($order->discount) }} ₫
                    </td>
                </tr>
                @endif
                @if($order->tax_amount > 0)
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Thuế:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; text-align:right;">
                        {{ number_format($order->tax_amount) }} ₫
                    </td>
                </tr>
                @endif
                @if($order->shipping_fee > 0)
                <tr>
                    <td style="padding:8px 0; color:#666; font-size:14px;">Phí vận chuyển:</td>
                    <td style="padding:8px 0; color:#333; font-size:14px; text-align:right;">
                        {{ number_format($order->shipping_fee) }} ₫
                    </td>
                </tr>
                @endif
                <tr style="border-top:2px solid #333;">
                    <td style="padding:12px 0; color:#333; font-size:16px; font-weight:600;">Tổng cộng:</td>
                    <td style="padding:12px 0; color:#333; font-size:18px; font-weight:700; text-align:right;">
                        {{ number_format($order->total) }} ₫
                    </td>
                </tr>
            </table>
        </div>

        <!-- Track Order Button -->
        <div style="text-align:center; margin:30px 0;">
            <a href="{{ route('client.order.track', ['code' => $order->code]) }}" 
               style="display:inline-block; background:#007bff; color:#fff; text-decoration:none; 
                      padding:12px 30px; border-radius:6px; font-size:16px; font-weight:600;">
                Theo dõi đơn hàng
            </a>
        </div>

        @if($order->note)
        <div style="background:#fff3cd; border-left:4px solid #ffc107; padding:15px; margin:20px 0; border-radius:4px;">
            <strong style="color:#856404;">Ghi chú:</strong>
            <p style="color:#856404; margin:5px 0 0; font-size:14px;">{{ $order->note }}</p>
        </div>
        @endif

        <!-- Footer -->
        <hr style="margin:30px 0; border:none; border-top:1px solid #ddd;">

        <p style="font-size:14px; color:#666; line-height:1.6; margin-bottom:10px;">
            Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi qua email hoặc hotline.
        </p>

        <p style="font-size:13px; color:#999; text-align:center; margin-top:20px;">
            Thư này được gửi tự động từ hệ thống, vui lòng không trả lời.<br>
            &copy; {{ date('Y') }} {{ config('app.name') }} Team
        </p>
    </div>

</body>
</html>

