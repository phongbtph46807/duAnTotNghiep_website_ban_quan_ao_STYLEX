@extends('client.layout.layout')
@section('title', 'Tra cứu đơn hàng - ' . env('APP_NAME'))
@section('content')
<div class="container p-t-60 p-b-60">
  <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="co-card co-card__body">
            <h2 class="co-title">Tra cứu trạng thái đơn hàng</h2>
            <form method="get" action="" class="m-b-30">
                <div class="co-grid">
                    <div class="co-col-9">
                        <input name="code" class="co-input" value="{{ request('code') }}" placeholder="Nhập mã đơn hàng hoặc số điện thoại">
                    </div>
                    <div class="co-col-3">
                        <button class="btn-primary-x">Tra cứu</button>
                    </div>
                </div>
            </form>
            @if(isset($order))
                <div class="m-b-24"><b>Mã đơn hàng:</b> {{ $order->code ?? $order->id }}<br>
                    <b>Thời gian đặt:</b> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <b>Trạng thái:</b> <span class="badge badge-info">{{ $order->status_label ?? 'Đang xử lý' }}</span>
                </div>
                <div class="m-b-20">
                  <b>Người nhận:</b> {{ $order->full_name }}<br>
                  <b>SĐT:</b> {{ $order->phone }}<br>
                  <b>Địa chỉ:</b> {{ $order->address }}
                </div>
                <div class="m-b-20">
                  <b>Sản phẩm:</b>
                  <ul>
                    @foreach($order->items as $item)
                      <li>{{ $item->product->name }}
                        @if($item->variant)
                          ({{ $item->variant->size->name ?? '' }} {{ $item->variant->color->name ?? '' }} {{ $item->variant->texture->name ?? '' }})
                        @endif
                        - SL: {{ $item->quantity }} - Giá: {{ number_format($item->price, 0, ',', '.') }}₫
                      </li>
                    @endforeach
                  </ul>
                </div>
                <div><b>Tổng tiền:</b> {{ number_format($order->total, 0, ',', '.') }}₫<br>
                <b>Phương thức thanh toán:</b> {{ $order->payment_method == 'cod' ? 'COD' : 'Online' }}</div>
            @elseif(request()->has('code'))
                <div class="co-hint">Không tìm thấy đơn hàng! Kiểm tra lại mã hoặc số điện thoại.</div>
            @endif
        </div>
    </div>
  </div>
</div>
@endsection
