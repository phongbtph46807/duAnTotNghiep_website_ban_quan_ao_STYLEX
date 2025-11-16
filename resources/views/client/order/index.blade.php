@extends('client.layout.layout')
@section('title', 'Đơn hàng của tôi - ' . env('APP_NAME'))
@section('content')
    <style>
        .table-history {
            width: 100%;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(103, 119, 239, .07);
            font-size: 15px;
            overflow: hidden;
        }

        .table-history th,
        .table-history td {
            padding: 14px 12px;
            text-align: left;
            vertical-align: middle;
        }

        .table-history th {
            color: #3b3f51;
            font-weight: 800;
            background: #f8faff;
            letter-spacing: 0.5px;
            font-size: 15.5px;
        }

        .table-history tr {
            border-bottom: 1px solid #f0f0f6;
            transition: background .13s;
        }

        .table-history tr:last-child {
            border-bottom: none;
        }

        .table-history tbody tr:hover {
            background: #f5f7fe;
        }

        .table-history td {
            color: #222;
        }

        .co-badge {
            display: inline-block;
            padding: 5px 14px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 13px;
            background: #eceff8;
            color: #556;
            min-width: 80px;
            text-align: center;
        }

        .badge-secondary {
            background: #eceff8;
            color: #555;
        }

        .badge-info {
            background: #eff4fa;
            color: #2963c8;
        }

        .badge-success {
            background: #e6ffed;
            color: #27ae60;
        }

        .badge-danger {
            background: #ffd8dd;
            color: #d9203c;
        }

        .btn-primary-x {
            background: #4d5ae5;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 7px 16px;
            font-size: 14px;
            font-weight: 700;
            transition: background .2s;
        }

        .btn-primary-x:hover {
            background: #3547b5;
            color: #fff;
        }

        @media (max-width:700px) {
            .table-history {
                font-size: 14px;
            }

            .table-history th,
            .table-history td {
                padding: 11px 4px;
            }
        }

        .review-modal .modal-dialog {
            margin-top: 150px !important;
        }

        <style>.image-upload-area {
            background-color: #fafafa;
            transition: all 0.2s ease;
        }

        .image-upload-area:hover {
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .preview-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            transition: 0.2s;
        }

        .preview-img:hover {
            transform: scale(1.05);
        }

        .remove-img-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 18px;
            cursor: pointer;
        }

        .preview-wrapper {
            position: relative;
            display: inline-block;
        }
    </style>

    </style>
    <div class="container p-t-60 p-b-60">
        <h2 class="co-title">Đơn hàng của tôi</h2>
        <div style="margin-bottom:24px;"></div>
        <div class="co-card">
            <div class="co-card__body">
                @if (count($orders))
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td style="font-weight:700;font-size:16px;">{{ $order->code ?? $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="font-weight:700;color:#4d5ae5;">
                                            {{ number_format($order->total, 0, ',', '.') }}₫</td>
                                        <td>
                                            @if ($order->payment_method == 'cod')
                                                <span style="color:#222">COD</span>
                                            @else
                                                <img src="https://static.mservice.io/img/logo-momo.png" alt="MoMo"
                                                    style="height:20px;vertical-align:middle;margin-right:4px;"> Online
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $label = 'Đang xử lý';
                                                $cls = 'badge-info';
                                                if ($order->status == 'pending') {
                                                    $label = 'Chờ xác nhận';
                                                    $cls = 'badge-secondary';
                                                } elseif ($order->status == 'processing') {
                                                    $label = 'Đang xử lý';
                                                    $cls = 'badge-info';
                                                } elseif ($order->status == 'completed') {
                                                    $label = 'Đã giao';
                                                    $cls = 'badge-success';
                                                } elseif ($order->status == 'cancelled') {
                                                    $label = 'Đã hủy';
                                                    $cls = 'badge-danger';
                                                }
                                            @endphp
                                            <span class="co-badge {{ $cls }}">{{ $label }}</span>
                                        </td>
                                        <td><a href="{{ route('client.order.track', ['code' => $order->code ?? $order->id]) }}"
                                                class="btn-primary-x">Xem chi tiết</a></td>
                                        <td>
                                            
                                                @foreach ($order->items as $item)
                                                    @php
                                                        $product = $item->product;
                                                        $variant = $item->variant;
                                                        $hasReviewed = $product->reviews->isNotEmpty();
                                                    @endphp
                                                    {{-- @php
                                                        logger()->info('Order item variant test', [
                                                            'order_item_id' => $item->id,
                                                            'variant_id' => $variant->id ?? null,
                                                            'product_id' => $product->id ?? null,
                                                        ]);
                                                    @endphp --}}
                                                    @if ($order->status == 'completed' && !$hasReviewed)
                                                    <div class="d-flex align-items-center mb-3 border p-2 rounded">


                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal{{ $item->id }}">
                                                            Đánh giá ngay
                                                        </button>
                                                    </div>
                                                    @else
                                                         <span class="text-success">Bạn đã đánh giá sản phẩm này.</span>
                                                     @endif
                                                    <!-- Modal đánh giá -->
                                                    <div class="modal fade review-modal" id="reviewModal{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="reviewModalLabel{{ $item->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <form class="modal-content review-form" method="POST"
                                                                action="{{ route('client.order.sendReviewOrder') }}">
                                                                @csrf
                                                                <input type="hidden" name="order_id"
                                                                    value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id"
                                                                    value="{{ $product->id }}">
                                                                <input type="hidden" name="product_variant_id"
                                                                    value="{{ $variant->id ?? '' }}">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        Đánh giá sản phẩm:
                                                                        <strong>{{ $product->name }}</strong>
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                                </div>

                                                                <div class="modal-body">

                                                                    {{-- Đánh giá tổng thể --}}
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Đánh giá tổng
                                                                            thể</label>
                                                                        <div class="star-input">
                                                                            @for ($i = 5; $i >= 1; $i--)
                                                                                <input type="radio" name="rating"
                                                                                    id="rating{{ $item->id }}_{{ $i }}"
                                                                                    value="{{ $i }}">
                                                                                <label
                                                                                    for="rating{{ $item->id }}_{{ $i }}">&#9733;</label>
                                                                            @endfor
                                                                        </div>
                                                                    </div>

                                                                    {{-- Trải nghiệm chi tiết --}}
                                                                    <hr>
                                                                    <label class="form-label fw-bold">Đánh giá theo trải
                                                                        nghiệm</label>
                                                                    <div class="row g-3">
                                                                        {{-- Chất liệu vải --}}
                                                                        <div class="col-md-4">
                                                                            <div class="criterion-box">
                                                                                <small>Chất liệu vải</small>
                                                                                <div class="star-input">
                                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                                        <input type="radio"
                                                                                            name="experiences[fabric][rating]"
                                                                                            id="fabric{{ $item->id }}_{{ $i }}"
                                                                                            value="{{ $i }}">
                                                                                        <label
                                                                                            for="fabric{{ $item->id }}_{{ $i }}">&#9733;</label>
                                                                                    @endfor
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    name="experiences[fabric][criterion]"
                                                                                    value="Chất liệu vải">
                                                                            </div>
                                                                        </div>

                                                                        {{-- Độ vừa vặn --}}
                                                                        <div class="col-md-4">
                                                                            <div class="criterion-box">
                                                                                <small>Độ vừa vặn</small>
                                                                                <div class="star-input">
                                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                                        <input type="radio"
                                                                                            name="experiences[fit][rating]"
                                                                                            id="fit{{ $item->id }}_{{ $i }}"
                                                                                            value="{{ $i }}">
                                                                                        <label
                                                                                            for="fit{{ $item->id }}_{{ $i }}">&#9733;</label>
                                                                                    @endfor
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    name="experiences[fit][criterion]"
                                                                                    value="Độ vừa vặn">
                                                                            </div>
                                                                        </div>

                                                                        {{-- Màu sắc --}}
                                                                        <div class="col-md-4">
                                                                            <div class="criterion-box">
                                                                                <small>Màu sắc</small>
                                                                                <div class="star-input">
                                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                                        <input type="radio"
                                                                                            name="experiences[color][rating]"
                                                                                            id="color{{ $item->id }}_{{ $i }}"
                                                                                            value="{{ $i }}">
                                                                                        <label
                                                                                            for="color{{ $item->id }}_{{ $i }}">&#9733;</label>
                                                                                    @endfor
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    name="experiences[color][criterion]"
                                                                                    value="Màu sắc">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Tag trải nghiệm --}}
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Chọn tiêu chí nổi
                                                                            bật</label><br>
                                                                        @foreach (['Chất liệu mềm mại', 'Form áo vừa vặn', 'Màu sắc tươi sáng'] as $tag)
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox" name="tags[]"
                                                                                    value="{{ $tag }}"
                                                                                    id="tag{{ $item->id }}_{{ Str::slug($tag) }}">
                                                                                <label class="form-check-label"
                                                                                    for="tag{{ $item->id }}_{{ Str::slug($tag) }}">{{ $tag }}</label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>

                                                                    {{-- Nội dung --}}
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nội dung đánh giá</label>
                                                                        <textarea name="content" class="form-control" rows="4"
                                                                            placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này..."></textarea>
                                                                    </div>
                                                                    {{-- Hình ảnh minh họa --}}
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Hình ảnh minh
                                                                            họa</label>
                                                                        <div
                                                                            class="image-upload-area border rounded p-3 text-center position-relative">
                                                                            <div id="previewContainer{{ $item->id }}"
                                                                                class="d-flex flex-wrap justify-content-start gap-2 mb-2">
                                                                            </div>

                                                                            <label for="reviewImages{{ $item->id }}"
                                                                                class="btn btn-outline-danger btn-sm rounded-pill">
                                                                                <i class="bi bi-camera"></i> Thêm ảnh
                                                                            </label>
                                                                            <input type="file" name="media[]"
                                                                                id="reviewImages{{ $item->id }}"
                                                                                accept="image/*" multiple hidden>
                                                                            <p class="text-muted small mt-2 mb-0">Tối đa 5
                                                                                ảnh, dung lượng mỗi ảnh ≤ 2MB</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-danger">Gửi đánh
                                                                        giá</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                           

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="co-hint">Bạn chưa có đơn hàng nào.</div>
                @endif
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInputs = document.querySelectorAll('input[type="file"][name="media[]"]');

    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const previewContainer = document.getElementById('previewContainer' + this.id.replace('reviewImages', ''));
            previewContainer.innerHTML = ''; // xóa ảnh cũ nếu có

            const files = Array.from(this.files).slice(0, 5); // tối đa 5 file
            files.forEach(file => {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    alert(file.name + ' vượt quá 2MB!');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.classList.add('border', 'rounded');
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('review_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: '{{ session('review_success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@endsection
