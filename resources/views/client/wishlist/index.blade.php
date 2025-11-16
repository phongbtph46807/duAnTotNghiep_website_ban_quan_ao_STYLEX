@extends('client.layout.layout')

@section('title', 'Danh sách yêu thích')

@section('content')

<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ route('home') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Trang chủ
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            Danh sách yêu thích
        </span>
    </div>
</div>

<div class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 m-lr-auto m-b-50">
                <div class="m-l-25 m-r--38 m-lr-0-xl">

                    <h4 class="mtext-109 cl2 p-b-30">
                        Sản phẩm bạn đã thích
                    </h4>

                    @if(isset($products) && $products->count() > 0)
                    <div class="wrap-table-shopping-cart">
                        <table class="table-shopping-cart">
                            <tr class="table_head">
                                <th class="column-1">Hình ảnh</th>
                                <th class="column-2">Sản phẩm</th>
                                <th class="column-3">Giá</th>
                                <th class="column-4">Trạng thái</th>
                                <th class="column-5" style="text-align: center;">Hành động</th>
                            </tr>

                            @foreach($products as $product)
                            <tr class="table_row wishlist-item-row">
                                <td class="column-1">
                                    <div class="how-itemcart1">
                                        <img src="{{ $product->default_image_url }}" alt="{{ $product->name }}">
                                    </div>
                                </td>
                                <td class="column-2">
                                    <a href="{{ route('client.products.show', $product->id) }}" class="mtext-106 cl4 hov-cl1 trans-04">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="column-3">
                                    @if($product->price_sale && $product->price_sale < $product->price)
                                        <span style="text-decoration: line-through; color: #999; font-size: 13px; display:block">
                                            {{ number_format($product->price) }}đ
                                        </span>
                                        <span style="color: #c0392b;">
                                            {{ number_format($product->price_sale) }}đ
                                        </span>
                                        @else
                                        {{ number_format($product->price) }}đ
                                        @endif
                                </td>
                                <td class="column-4">
                                    @if($product->is_active)
                                    <span class="badge badge-success" style="padding: 8px 10px; font-size: 12px;">Còn hàng</span>
                                    @else
                                    <span class="badge badge-secondary" style="padding: 8px 10px; font-size: 12px;">Hết hàng</span>
                                    @endif
                                </td>
                                <td class="column-5" style="text-align: center;">
                                    <button
                                        type="button"
                                        class="js-remove-from-wishlist flex-c-m stext-101 cl2 size-101 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer"
                                        data-product-id="{{ $product->id }}"
                                        style="background-color: #c0392b; color: white; height: 40px; min-width: 100px;
                                        display: inline-block; border: none; cursor: pointer;">Xóa
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="flex-w flex-c-m m-t-30">
                       {{ $products->links('pagination::bootstrap-4') }}
                    </div>

                    @else
                    <div class="text-center p-t-50 p-b-50 border rounded bg-light">
                        <i class="zmdi zmdi-favorite-outline cl2 m-b-20" style="font-size: 60px;"></i>
                        <h4 class="mtext-105 cl2 p-b-15">Danh sách trống</h4>
                        <p class="stext-115 cl6 p-b-30">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>

                        <a href="{{ route('client.products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-auto" style="width: 220px;">
                            Mua sắm ngay
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@endsection