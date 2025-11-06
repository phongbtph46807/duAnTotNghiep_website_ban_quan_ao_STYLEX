<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn đơn hàng <?php echo e($d['order_code'] ?? ''); ?></title>
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
        <h1>Hóa đơn đơn hàng <?php echo e($d['order_code'] ?? ''); ?></h1>
        <div class="sub">Cảm ơn bạn đã mua sắm tại STYLEX</div>
    </div>

    <div class="section grid">
        <div>
            <div class="label">Khách hàng</div>
            <div class="value">
                <?php echo e($d['full_name']); ?><br>
                <?php echo e($d['phone']); ?><br>
                <?php if(!empty($d['email'])): ?> <?php echo e($d['email']); ?><br><?php endif; ?>
            </div>
        </div>
        <div>
            <div class="label">Giao hàng</div>
            <div class="value">
                <?php echo e($d['address']); ?><br>
                <?php echo e($d['city']); ?>

            </div>
        </div>
    </div>

    <div class="section">
        <div class="label">Thông tin đơn</div>
        <div class="grid" style="margin-top:8px;">
            <div class="value">Mã đơn: <strong><?php echo e($d['order_code']); ?></strong></div>
            <div class="value right">Ngày đặt: <?php echo e($d['placed_at']); ?></div>
        </div>
        <div class="grid" style="margin-top:6px;">
            <div class="value">Thanh toán: <?php echo e(strtoupper($d['payment_method'])); ?> (<?php echo e($d['payment_status']); ?>)</div>
            <div class="value right">Trạng thái: <?php echo e(ucfirst($d['status'])); ?></div>
        </div>
        <?php if(!empty($d['note'])): ?>
            <div class="value" style="margin-top:8px;">
                Ghi chú: <span class="muted"><?php echo e($d['note']); ?></span>
            </div>
        <?php endif; ?>
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
            <?php $__currentLoopData = $d['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <div><?php echo e($row['product_name']); ?></div>
                        <?php if(!empty($row['variant_label'])): ?>
                            <div class="muted" style="font-size:12px;"><?php echo e($row['variant_label']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="right"><?php echo e($row['quantity']); ?></td>
                    <td class="right"><?php echo e(number_format($row['unit_price'])); ?> đ</td>
                    <td class="right"><?php echo e(number_format($row['line_total'])); ?> đ</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="right muted">Tạm tính</td>
                <td class="right"><?php echo e(number_format($d['subtotal'])); ?> đ</td>
            </tr>
            <tr>
                <td colspan="3" class="right muted">Phí vận chuyển</td>
                <td class="right"><?php echo e(number_format($d['shipping_fee'])); ?> đ</td>
            </tr>
            <?php if(!empty($d['discount']) && (int)$d['discount'] > 0): ?>
            <tr>
                <td colspan="3" class="right muted">Giảm giá</td>
                <td class="right">-<?php echo e(number_format($d['discount'])); ?> đ</td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td colspan="3" class="right">Tổng cộng</td>
                <td class="right"><?php echo e(number_format($d['total'])); ?> đ</td>
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


<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/duAnTotNghiep_website_ban_quan_ao_STYLEX/resources/views/admin/mails/invoice_order.blade.php ENDPATH**/ ?>