@php
    // Lấy ra các con CÓ TRẠNG THÁI (status) là 1 (hoạt động)
    $activeChildren = $category->childrenRecursive->where('status', 1);
@endphp

@if ($activeChildren->isNotEmpty())
    {{-- NẾU CÓ CON: Hiển thị <li> với menu con --}}
    <li class="menu-item-has-children {{ $isSubmenu ? 'dropdown-submenu' : '' }}">
        
        <!-- Link sẽ là: /products?category=ID_CUA_CHA  -->
        <a href="{{ route('client.products.index', ['category' => $category->id]) }}">{{ $category->name }}</a>

        <ul class="sub-menu">
            @foreach ($activeChildren as $childCategory)
                 <!-- Đệ quy: Tự gọi lại chính nó cho các cấp con  -->
                @include('client.partials.menu_item', [
                    'category' => $childCategory,
                    'isSubmenu' => true
                ])
            @endforeach
        </ul>
    </li>
@else
    <!-- NẾU KHÔNG CÓ CON: Hiển thị <li> bình thường  -->
    <li>
        <a href="{{ route('client.products.index', ['category' => $category->id]) }}">{{ $category->name }}</a>
    </li>
@endif