@extends('client.layout.layout')
@section('title', 'Đơn hàng của tôi - ' . env('APP_NAME'))

@section('content')
<style>
/* === Giữ nguyên CSS cũ của bạn === */
.table-history{width:100%;background:#fff;border-radius:14px;box-shadow:0 6px 24px rgba(103,119,239,.07);font-size:15px;overflow:hidden}
.table-history th,.table-history td{padding:14px 12px;text-align:left;vertical-align:middle}
.table-history th{color:#3b3f51;font-weight:800;background:#f8faff;letter-spacing:.5px;font-size:15.5px}
.table-history tr{border-bottom:1px solid #f0f0f6;transition:background .13s}
.table-history tr:last-child{border-bottom:none}
.table-history tbody tr:hover{background:#f5f7fe}
.table-history td{color:#222}
.co-badge{display:inline-block;padding:5px 14px;font-size:13px;font-weight:700;border-radius:13px;background:#eceff8;color:#556;min-width:80px;text-align:center}
.badge-secondary{background:#eceff8;color:#555}
.badge-info{background:#eff4fa;color:#2963c8}
.badge-success{background:#e6ffed;color:#27ae60}
.badge-danger{background:#ffd8dd;color:#d9203c}
.btn-primary-x{background:#4d5ae5;color:#fff;border:none;border-radius:7px;padding:7px 16px;font-size:14px;font-weight:700;transition:background .2s}
.btn-primary-x:hover{background:#3547b5}
.btn-danger-x{background:#e74c3c;color:#fff;border:none;border-radius:7px;padding:7px 12px;font-size:13px;font-weight:600}
.btn-danger-x:hover{background:#c0392b}
@media(max-width:700px){.table-history{font-size:14px}.table-history th,.table-history td{padding:11px 4px}}

/* Toast */
.toast{position:fixed;bottom:30px;right:30px;z-index:1050;min-width:300px}
.toast-success{background:#27ae60;color:#fff}
.toast-error{background:#e74c3c;color:#fff}
.toast-body{padding:16px 20px;border-radius:8px;box-shadow:0 8px 25px rgba(0,0,0,.15);font-weight:500}
</style>

<div class="container p-t-60 p-b-60">
  <h2 class="co-title">Đơn hàng của tôi</h2>
  <div style="margin-bottom:24px;"></div>

  <!-- Toast thông báo -->
  @if(session('success'))
  <div class="toast toast-success" id="toast">
    <div class="toast-body">
      {{ session('success') }}
    </div>
  </div>
  @endif
  @if(session('error'))
  <div class="toast toast-error" id="toast">
    <div class="toast-body">
      {{ session('error') }}
    </div>
  </div>
  @endif

  <div class="co-card"><div class="co-card__body">
    @if(count($orders))
    <div class="table-responsive">
      <table class="table-history">
        <thead>
          <tr>
            <th># Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
          <tr>
            <td style="font-weight:700;font-size:16px;">{{ $order->code ?? $order->id }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td style="font-weight:700;color:#4d5ae5;">
              {{ number_format($order->total, 0, ',', '.') }}₫
            </td>
            <td>
              @if($order->payment_method=='cod')
                <span style="color:#222">COD</span>
              @else
                <img src="https://static.mservice.io/img/logo-momo.png" alt="MoMo"
                     style="height:20px;vertical-align:middle;margin-right:4px;"> Online
              @endif
            </td>
            <td>
              @php
                $label = 'Đang xử lý'; $cls='badge-info';
                if($order->status=='pending'){ $label='Chờ xác nhận'; $cls='badge-secondary'; }
                elseif($order->status=='processing'){ $label='Đang xử lý'; $cls='badge-info'; }
                elseif($order->status=='shipped'){ $label='Đã giao'; $cls='badge-success'; }
                elseif($order->status=='cancelled'){ $label='Đã hủy'; $cls='badge-danger'; }
              @endphp
              <span class="co-badge {{ $cls }}">{{ $label }}</span>
            </td>

            <td class="text-right">
              <!-- Nút Xem chi tiết -->
              <a href="{{ route('client.order.track', ['code' => $order->code ?? $order->id]) }}"
                 class="btn-primary-x">Xem chi tiết</a>

              <!-- Nút XOÁ (chỉ hiện khi pending/processing) -->
              @if(in_array($order->status, ['pending', 'processing']))
                <form action="{{ route('client.order.destroy', $order->id) }}"
                      method="POST" style="display:inline-block;margin-left:8px;"
                      onsubmit="return confirm('Hủy đơn hàng #{{ $order->code ?? $order->id }}?\nHành động này không thể hoàn tác!')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-danger-x">Hủy</button>
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
      <div class="co-hint">Bạn chưa có đơn hàng nào.</div>
    @endif
  </div></div>
</div>

<!-- Script ẩn toast sau 4s -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toast = document.getElementById('toast');
  if (toast) {
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .4s';
      setTimeout(() => toast.remove(), 500);
    }, 4000);
  }
});
</script>
@endsection