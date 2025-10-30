
<?php $__env->startSection('title', 'Tra cứu đơn hàng - ' . env('APP_NAME')); ?>
<?php $__env->startSection('content'); ?>
<div class="container p-t-60 p-b-60">
  <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="co-card co-card__body">
            <h2 class="co-title">Tra cứu trạng thái đơn hàng</h2>
            <form method="get" action="" class="m-b-30">
                <div class="co-grid">
                    <div class="co-col-9">
                        <input name="code" class="co-input" value="<?php echo e(request('code')); ?>" placeholder="Nhập mã đơn hàng hoặc số điện thoại">
                    </div>
                    <div class="co-col-3">
                        <button class="btn-primary-x">Tra cứu</button>
                    </div>
                </div>
            </form>
            <?php if(isset($order)): ?>
                <div class="m-b-24"><b>Mã đơn hàng:</b> <?php echo e($order->code ?? $order->id); ?><br>
                    <b>Thời gian đặt:</b> <?php echo e($order->created_at->format('d/m/Y H:i')); ?><br>
                    <b>Trạng thái:</b> <span class="badge badge-info"><?php echo e($order->status_label ?? 'Đang xử lý'); ?></span>
                </div>
                <div class="m-b-20">
                  <b>Người nhận:</b> <?php echo e($order->full_name); ?><br>
                  <b>SĐT:</b> <?php echo e($order->phone); ?><br>
                  <b>Địa chỉ:</b> <?php echo e($order->address); ?>

                </div>
                <div class="m-b-20">
                  <b>Sản phẩm:</b>
                  <ul>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <li><?php echo e($item->product->name); ?>

                        <?php if($item->variant): ?>
                          (<?php echo e($item->variant->size->name ?? ''); ?> <?php echo e($item->variant->color->name ?? ''); ?> <?php echo e($item->variant->texture->name ?? ''); ?>)
                        <?php endif; ?>
                        - SL: <?php echo e($item->quantity); ?> - Giá: <?php echo e(number_format($item->price, 0, ',', '.')); ?>₫
                      </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </div>
                <div><b>Tổng tiền:</b> <?php echo e(number_format($order->total, 0, ',', '.')); ?>₫<br>
                <b>Phương thức thanh toán:</b> <?php echo e($order->payment_method == 'cod' ? 'COD' : 'Online'); ?></div>
            <?php elseif(request()->has('code')): ?>
                <div class="co-hint">Không tìm thấy đơn hàng! Kiểm tra lại mã hoặc số điện thoại.</div>
            <?php endif; ?>
        </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/order/track.blade.php ENDPATH**/ ?>