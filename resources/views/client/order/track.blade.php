@extends('client.layout.layout')
@section('title', 'Tra cứu đơn hàng - ' . env('APP_NAME'))
@section('content')
<style>
.co-track-card{max-width:700px;margin:40px auto;background:#fff;border-radius:16px;box-shadow:0 6px 32px rgba(69,116,209,.10);padding:36px 32px 32px 32px;}
.co-track-title{font-size:2rem;text-align:center;font-weight:800;color:#3247ad;margin-bottom:32px;letter-spacing:1px;}
.co-badge { display:inline-block; padding:8px 20px; font-size:1rem; font-weight:800; border-radius:18px; background: #eceff8; color:#556; min-width:90px; text-align:center; letter-spacing:.3px; }
.badge-secondary { background:#f5f6fa; color:#787882; }
.badge-info { background:#3c6ac6; color:#fff; }
.badge-success { background:#28bb7c; color:#fff; }
.badge-danger { background:#ff486e; color:#fff; }
.table-products{width:100%;margin-bottom:20px;}
.table-products th,.table-products td{ padding:8px 8px; font-size:15px;}
.table-products th{ background:#f8faff; color:#233; font-weight:700; }
.table-products tr{ border-bottom:1px solid #f2f3f9;}
.table-products td{ color:#222; }
.total-row{font-weight:900;font-size:1.15rem;color:#52a0fd;}
@media(max-width:900px){.co-track-card{padding:21px 8px 10px 8px;}}
</style>
<div class="container">
  <div class="co-track-card">
    <div class="co-track-title">Thông tin đơn hàng</div>
    <form method="get" action="" class="m-b-30" style="max-width:420px;margin:auto;display:flex;gap:10px 4px;">
        <input name="code" class="co-input" value="{{ request('code') }}" placeholder="Nhập mã đơn hàng hoặc số điện thoại">
        <button class="btn-primary-x">Tra cứu</button>
    </form>
    @if(isset($order) && $order)
      <div style="margin-bottom:20px;text-align:center;">
        <span style="font-size:19px;color:#4d5ae5;font-weight:800;letter-spacing:1.2px;">#{{ $order->code ?? $order->id }}</span><br>
        <span style="color:#333">Đặt lúc: {{ $order->created_at->format('d/m/Y H:i') }}</span><br>
        @php
        $label = 'Đang xử lý'; $cls='badge-info';
        if($order->status=='pending'){ $label='Chờ xác nhận'; $cls='badge-secondary'; }
        elseif($order->status=='processing'){ $label='Đang xử lý'; $cls='badge-info'; }
        elseif($order->status=='shipped'){ $label='Đã giao'; $cls='badge-success'; }
        elseif($order->status=='cancelled'){ $label='Đã hủy'; $cls='badge-danger'; }
        @endphp
        <span class="co-badge {{ $cls }}">{{ $label }}</span>
      </div>
      <div style="margin-bottom:22px;">
        <b>Người nhận:</b> {{ $order->full_name }}<br>
        <b>SĐT:</b> {{ $order->phone }}<br>
        <b>Địa chỉ:</b> {{ $order->address }}
      </div>
      <div style="margin-bottom:18px;">
        <b>Danh sách sản phẩm:</b>
        <div class="table-responsive"><table class="table-products">
          <thead>
            <tr><th>Tên SP</th><th>Biến thể</th><th>SL</th><th>Giá</th></tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td>{{ $item->product->name }}</td>
              <td>
                @php 
                  $bs = []; 
                  // Lấy size và color từ variant đã chọn
                  if($item->variant) {
                    if($item->variant->size) $bs[] = 'Size: ' . $item->variant->size->name; 
                    if($item->variant->color) $bs[] = 'Màu: ' . $item->variant->color->name; 
                  }
                  
                  // Lấy tất cả chất liệu từ productVariants của sản phẩm
                  $allTextures = [];
                  if($item->product && $item->product->productVariants && $item->product->productVariants->count() > 0) {
                    foreach($item->product->productVariants as $variant) {
                      if($variant->texture && $variant->texture->name) {
                        $allTextures[] = $variant->texture->name;
                      }
                    }
                    $allTextures = array_unique($allTextures);
                    sort($allTextures);
                  }
                  
                  // Thêm chất liệu vào danh sách
                  if(count($allTextures) > 0) {
                    $bs[] = 'Chất liệu: ' . implode(', ', $allTextures);
                  }
                @endphp
                {{ implode(' | ', $bs) }}
              </td>
              <td style="text-align:center;">{{ $item->quantity }}</td>
              <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
          </tbody>
        </table></div>
      </div>
      <div class="total-row text-right" style="text-align:right">Tổng tiền: <span style="color:#4d5ae5">{{ number_format($order->total, 0, ',', '.') }}₫</span></div>
      <div>Phương thức thanh toán: @if($order->payment_method=='cod')<span style="font-weight:600;color:#333">COD</span>@else <img src="https://static.mservice.io/img/logo-momo.png" alt="MoMo" style="height:18px;vertical-align:middle;margin-right:4px;"> Online @endif</div>
    @elseif(request()->has('code'))
      <div class="co-hint" style="color:#c44;font-size:17px;font-weight:600;text-align:center;">Không tìm thấy đơn hàng! Kiểm tra lại mã hoặc số điện thoại.</div>
    @endif
  </div>
</div>
@endsection
