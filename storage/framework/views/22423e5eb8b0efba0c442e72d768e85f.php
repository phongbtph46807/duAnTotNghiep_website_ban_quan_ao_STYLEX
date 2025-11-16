<?php $__env->startSection('title', $product->name . ' - ' . env('APP_NAME')); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .product-image {
            width: 100%;
            height: auto;
            max-width: 100%;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            -ms-interpolation-mode: nearest-neighbor;
        }

        .wrap-pic-w img {
            width: 100% !important;
            height: auto !important;
            max-width: 100% !important;
            object-fit: contain !important;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            -ms-interpolation-mode: nearest-neighbor;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="<?php echo e(route('home')); ?>" class="stext-109 cl8 hov-cl1 trans-04">
                Trang Chủ
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <a href="<?php echo e(route('client.products.index')); ?>" class="stext-109 cl8 hov-cl1 trans-04">
                Sản Phẩm
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                <?php echo e($product->name); ?>

            </span>
        </div>
    </div>

    <!-- Product Detail -->
    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb">
                                <?php if($product->productImages && $product->productImages->count() > 0): ?>
                                    <?php $__currentLoopData = $product->productImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="item-slick3" data-thumb="<?php echo e(Storage::url($image->image_path)); ?>">
                                            <div class="wrap-pic-w pos-relative">
                                                <img src="<?php echo e(Storage::url($image->image_path)); ?>" alt="<?php echo e($product->name); ?>"
                                                    class="product-image">

                                                <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                    href="<?php echo e(Storage::url($image->image_path)); ?>">
                                                    <i class="fa fa-expand"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="item-slick3" data-thumb="<?php echo e($product->default_image_url); ?>">
                                        <div class="wrap-pic-w pos-relative">
                                            <img src="<?php echo e($product->default_image_url); ?>" alt="<?php echo e($product->name); ?>"
                                                class="product-image">

                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                href="<?php echo e($product->default_image_url); ?>">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            <?php echo e($product->name); ?>

                        </h4>

                        <span class="mtext-106 cl2">
                            <?php if($product->price_sale && $product->price_sale < $product->price): ?>
                                <span class="fw-bold"><?php echo e(number_format($product->price_sale, 0, ',', '.')); ?>đ</span>
                                <span
                                    style="text-decoration: line-through; color: red;"><?php echo e(number_format($product->price, 0, ',', '.')); ?>đ</span>
                            <?php else: ?>
                                <?php echo e(number_format($product->price, 0, ',', '.')); ?>đ
                            <?php endif; ?>
                        </span>

                        <p class="stext-102 cl3 p-t-23">
                            <?php echo e($product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo.'); ?>

                        </p>

                        <!-- Product Options -->
                        <div class="p-t-33">
                            <?php if($product->productVariants && $product->productVariants->count() > 0): ?>
                                <?php
                                    $sizes = $product->productVariants->pluck('size.name')->unique()->filter();
                                    $colors = $product->productVariants->pluck('color.name')->unique()->filter();
                                    $textures = $product->productVariants->pluck('texture.name')->unique()->filter();
                                ?>

                                <?php if($sizes->count() > 0): ?>
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-203 flex-c-m respon6">
                                            Kích Thước
                                        </div>

                                        <div class="size-204 respon6-next">
                                            <div class="rs1-select2 bor8 bg0">
                                                <select class="js-select2" name="size" id="size-select">
                                                    <option value="">Chọn kích thước</option>
                                                    <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($size); ?>"><?php echo e($size); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($colors->count() > 0): ?>
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-203 flex-c-m respon6">
                                            Màu Sắc
                                        </div>

                                        <div class="size-204 respon6-next">
                                            <div class="rs1-select2 bor8 bg0">
                                                <select class="js-select2" name="color" id="color-select">
                                                    <option value="">Chọn màu sắc</option>
                                                    <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($color); ?>"><?php echo e($color); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($textures->count() > 0): ?>
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-203 flex-c-m respon6">
                                            Chất Liệu
                                        </div>

                                        <div class="size-204 respon6-next">
                                            <div class="stext-102 cl6" style="padding: 8px 0;">
                                                <span id="texture-display"><?php echo e($textures->implode(', ')); ?></span>
                                                <?php
                                                    $firstTexture = $textures->first();
                                                ?>
                                                <input type="hidden" name="texture" id="texture-select"
                                                    value="<?php echo e($firstTexture); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <div class="flex-w flex-r-m p-b-10" data-product-id="<?php echo e($product->id); ?>"
                                data-variants="<?php echo e(json_encode($product->productVariants)); ?>"
                                data-original-price="<?php echo e($product->price); ?>"
                                data-original-price-sale="<?php echo e($product->price_sale); ?>">
                                <form id="add-to-cart-form" method="POST" action="<?php echo e(route('client.cart.add')); ?>"
                                    data-ajax="1" class="size-204 flex-w flex-m respon6-next">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                                    <input type="hidden" name="variant_id" value="">
                                    <input type="hidden" name="size_name" value="">
                                    <input type="hidden" name="color_name" value="">
                                    <input type="hidden" name="texture_name" value="">
                                    <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                        <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-minus"></i>
                                        </div>

                                        <input class="mtext-104 cl3 txt-center num-product" type="number"
                                            name="quantity" value="1" min="1">

                                        <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-plus"></i>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                        Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Social Share -->
                        <div class="flex-w flex-m p-l-100 p-t-40 respon7">
                            <div class="flex-m bor9 p-r-10 m-r-11">
                                <a href="#"
                                    class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100"
                                    data-tooltip="Thêm vào yêu thích">
                                    <i class="zmdi zmdi-favorite"></i>
                                </a>
                            </div>

                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Facebook">
                                <i class="fa fa-facebook"></i>
                            </a>

                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Twitter">
                                <i class="fa fa-twitter"></i>
                            </a>

                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Google Plus">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <!-- Tab01 -->
                <div class="tab01">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Mô tả</a>
                        </li>

                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab">Thông tin bổ
                                sung</a>
                        </li>

                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">
                                Đánh giá
                                <?php if(isset($product->reviews_count)): ?>
                                    (<?php echo e($product->reviews_count); ?>)
                                <?php elseif(isset($product->reviews) && method_exists($product->reviews, 'count')): ?>
                                    (<?php echo e($product->reviews->count()); ?>)
                                <?php else: ?>
                                    (0)
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-t-43">
                        <!-- Description -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">
                                    <?php echo e($product->description ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại và phong cách độc đáo. Được làm từ những nguyên liệu tốt nhất, đảm bảo độ bền và tính thẩm mỹ cao.'); ?>

                                </p>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="tab-pane fade" id="information" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">
                                                Danh mục
                                            </span>

                                            <span class="stext-102 cl6 size-206">
                                                <?php echo e($product->category->name ?? 'N/A'); ?>

                                            </span>
                                        </li>

                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">
                                                Trạng thái
                                            </span>

                                            <span class="stext-102 cl6 size-206">
                                                <?php echo e($product->is_active ? 'Còn hàng' : 'Hết hàng'); ?>

                                            </span>
                                        </li>

                                        <?php if($product->productVariants && $product->productVariants->count() > 0): ?>
                                            <?php
                                                $sizes = $product->productVariants
                                                    ->pluck('size.name')
                                                    ->unique()
                                                    ->filter();
                                                $colors = $product->productVariants
                                                    ->pluck('color.name')
                                                    ->unique()
                                                    ->filter();
                                            ?>

                                            <?php if($colors->count() > 0): ?>
                                                <li class="flex-w flex-t p-b-7">
                                                    <span class="stext-102 cl3 size-205">
                                                        Màu sắc
                                                    </span>

                                                    <span class="stext-102 cl6 size-206">
                                                        <?php echo e($colors->implode(', ')); ?>

                                                    </span>
                                                </li>
                                            <?php endif; ?>

                                            <?php if($sizes->count() > 0): ?>
                                                <li class="flex-w flex-t p-b-7">
                                                    <span class="stext-102 cl3 size-205">
                                                        Kích thước
                                                    </span>

                                                    <span class="stext-102 cl6 size-206">
                                                        <?php echo e($sizes->implode(', ')); ?>

                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <div class="row">
                                <div class="m-lr-auto">
                                    <div class="p-b-30 m-lr-15-sm">
                                        <!-- Add review -->
                                        <div class="container container-custom bg-white p-4 rounded shadow-sm my-4">
                                            <h6 class="mb-4 fw-bold">Đánh giá <?php echo e($product->name); ?></h6>
                                            <?php
                                                $fullStars = floor($avgRating);
                                                $hasHalfStar = $avgRating - $fullStars >= 0.5;
                                            ?>
                                            <!-- Tổng quan đánh giá -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-4 col-md-3 text-center" style="width: 200px;">
                                                    <div class="fs-1 fw-bold text-danger">
                                                        <?php echo e($avgRating ?? 0); ?><span class="fs-2 text-secondary">/5</span>
                                                    </div>
                                                    
                                                    <span class="stars-inline" aria-label="<?php echo e($avgRating); ?> sao">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <?php if($i <= $fullStars): ?>
                                                                
                                                                <svg class="text-warning" viewBox="0 0 24 24"
                                                                    style="width:24px;height:24px;fill:currentColor;">
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                    9.24l-7.19-.61L12 2 9.19 8.63
                                                    2 9.24l5.46 4.73L5.82 21z" />
                                                                </svg>
                                                            <?php elseif($i == $fullStars + 1 && $hasHalfStar): ?>
                                                                
                                                                <svg viewBox="0 0 24 24" style="width:24px;height:24px;">
                                                                    <defs>
                                                                        <linearGradient id="half-star" x1="0%"
                                                                            x2="100%">
                                                                            <stop offset="50%" stop-color="gold" />
                                                                            <stop offset="50%" stop-color="lightgray" />
                                                                        </linearGradient>
                                                                    </defs>
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                    9.24l-7.19-.61L12 2 9.19 8.63
                                                    2 9.24l5.46 4.73L5.82 21z" fill="url(#half-star)" />
                                                                </svg>
                                                            <?php else: ?>
                                                                
                                                                <svg class="text-secondary" viewBox="0 0 24 24"
                                                                    style="width:24px;height:24px;fill:currentColor;">
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                    9.24l-7.19-.61L12 2 9.19 8.63
                                                    2 9.24l5.46 4.73L5.82 21z" />
                                                                </svg>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                    </span>
                                                    <div class="text-muted large"><?php echo e($product->reviews->count()); ?> lượt
                                                        đánh giá</div>
                                                </div>
                                                <div class="col-3 col-md-4" style="width: 400px;">
                                                    <?php $__currentLoopData = [5, 4, 3, 2, 1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="mb-1 d-flex align-items-center">
                                                            <span class="stars-inline"
                                                                aria-label="<?php echo e($star); ?> sao">
                                                                <?php echo e($star); ?>

                                                                <svg class="star-icon" viewBox="0 0 24 24"
                                                                    aria-hidden="true" focusable="false">
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                    9.24l-7.19-.61L12 2 9.19 8.63
                                                    2 9.24l5.46 4.73L5.82 21z" />
                                                                </svg>
                                                            </span>
                                                            <div class="rating-bar flex-grow-1 mx-2">
                                                                <div class="rating-bar-fill"
                                                                    style="width: <?php echo e($product->ratings_percent[$star]); ?>%;">
                                                                </div>
                                                            </div>
                                                            <span class="text-muted small" style="min-width: 70px;">
                                                                <?php echo e($product->ratings_count[$star]); ?> đánh giá
                                                            </span>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                                <div class="col-auto d-flex justify-content-center p-0"
                                                    style="height: 120px;">
                                                    <div class="vr mx-3" style="height: 100%; opacity: 0.5;"></div>
                                                </div>
                                                <div class="col-4 col-md-3 d-none d-md-block" style="width: 380px;">
                                                    <div class="fw-bold mb-3">Đánh giá theo trải nghiệm</div>

                                                    <?php $__currentLoopData = $product->experience_avg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $avg = $data['avg'];
                                                            $count = $data['count'];
                                                            $fullStars = floor($avg);
                                                            $hasHalfStar = $avg - $fullStars >= 0.5;
                                                        ?>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2 small">
                                                            <span><?php echo e($label); ?></span>
                                                            <span class="stars-inline"
                                                                aria-label="<?php echo e($avg); ?> sao">
                                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                                    <?php if($i <= $fullStars): ?>
                                                                        <svg class="text-warning" viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                9.24l-7.19-.61L12 2 9.19 8.63
                                                2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php elseif($i == $fullStars + 1 && $hasHalfStar): ?>
                                                                        <?php $gradientId = 'half-star-' . $loop->index . '-' . uniqid(); ?>
                                                                        <svg viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;">
                                                                            <defs>
                                                                                <linearGradient id="<?php echo e($gradientId); ?>"
                                                                                    x1="0%" x2="100%">
                                                                                    <stop offset="50%"
                                                                                        stop-color="gold" />
                                                                                    <stop offset="50%"
                                                                                        stop-color="lightgray" />
                                                                                </linearGradient>
                                                                            </defs>
                                                                            <path fill="url(#<?php echo e($gradientId); ?>)" d="M12 17.27L18.18 21l-1.64-7.03L22
                                                9.24l-7.19-.61L12 2 9.19 8.63
                                                2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php else: ?>
                                                                        <svg class="text-secondary" viewBox="0 0 24 24"
                                                                            style="width:20px;height:20px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                9.24l-7.19-.61L12 2 9.19 8.63
                                                2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php endif; ?>
                                                                <?php endfor; ?>
                                                                <span
                                                                    class="ms-1 text-black-50"><?php echo e(number_format($avg, 1)); ?>/5</span>
                                                                <span class="ms-2 text-muted">(<?php echo e($count); ?> đánh
                                                                    giá)</span>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </div>


                                            </div>

                                            <!-- Bộ lọc đánh giá -->
                                            <div class="filter-container mb-3">
                                                <button type="button" class="filter-btn active">Tất cả</button>
                                                <button type="button" class="filter-btn">Có hình ảnh/Video (345)</button>
                                                <button type="button" class="filter-btn">5 sao (300)</button>
                                                <button type="button" class="filter-btn">4 sao (200)</button>
                                                <button type="button" class="filter-btn">3 sao (321)</button>
                                                <button type="button" class="filter-btn">2 sao (129)</button>
                                                <button type="button" class="filter-btn">1 sao (55)</button>
                                            </div>

                                            <!-- Danh sách đánh giá -->
                                            <?php $__currentLoopData = $latestReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="mb-4 border-bottom pb-3">
                                                    <div class="d-flex gap-3">
                                                        <div class="avatar-circle avatar-d">
                                                            <?php if($review['user']['avatar']): ?>
                                                                <img src="<?php echo e($review['user']['avatar']); ?>"
                                                                    alt="<?php echo e($review['user']['name']); ?>"
                                                                    class="rounded-circle"
                                                                    style="width:40px;height:40px;">
                                                            <?php else: ?>
                                                                <?php echo e(substr($review['user']['name'], 0, 1)); ?>

                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="review-header">
                                                                <?php echo e($review['user']['name']); ?>

                                                                <div class="stars-inline"
                                                                    aria-label="<?php echo e($review['rating']); ?> sao">
                                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                                        <svg class="star-icon <?php echo e($i <= $review['rating'] ? 'text-warning' : 'text-light'); ?>"
                                                                            viewBox="0 0 24 24" aria-hidden="true"
                                                                            focusable="false"
                                                                            style="width:16px;height:16px;fill:currentColor;">
                                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22
                                                    9.24l-7.19-.61L12 2 9.19 8.63
                                                    2 9.24l5.46 4.73L5.82 21z" />
                                                                        </svg>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>
                                                            <?php if(!empty($review['tags'])): ?>
                                                                <div class="mt-1">
                                                                    <?php $__currentLoopData = $review['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <span
                                                                            style="background:#f1f1f1; padding:3px 8px; border-radius:6px; font-size:12px; margin-right:4px;">
                                                                            <?php echo e($tag); ?>

                                                                        </span>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div><small class="text-muted">Phân loại hàng:
                                                                    <?php echo e($review['variant'] ?? ''); ?></small></div>


                                                            <div class="review-subtitle"><?php echo e($review['comment']); ?></div>
                                                            <div class="review-time">
                                                                <i class="bi bi-clock"></i> <?php echo e($review['created_at']); ?>

                                                            </div>
                                                            <?php if(!empty($review['media'])): ?>
                                                                <div class="review-media mt-2 d-flex gap-2">
                                                                    <?php $__currentLoopData = $review['media']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mediaUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <img src="<?php echo e(Storage::url($mediaUrl)); ?>"
                                                                            alt="media"
                                                                            style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                            <!-- Nút xem tất cả -->
                                            <div class="text-center">
                                                <button class="btn btn-outline-secondary fw-semibold">
                                                    Xem tất cả đánh giá
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-arrow-right ms-2"
                                                        viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M10.146 12.354a.5.5 0 0 1 0-.708L13.793 8 10.146 4.354a.5.5 0 1 1
                                                                    .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708 0z" />
                                                        <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5
                                                                    0 0 1 2 8z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">
                <span class="stext-107 cl6 p-lr-25">
                    SKU: <?php echo e($product->id); ?>

                </span>

                <span class="stext-107 cl6 p-lr-25">
                    Danh mục: <?php echo e($product->category->name ?? 'N/A'); ?>

                </span>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if($relatedProducts && $relatedProducts->count() > 0): ?>
        <section class="sec-relate-product bg0 p-t-45 p-b-105">
            <div class="container">
                <div class="p-b-45">
                    <h3 class="ltext-106 cl5 txt-center">
                        Sản Phẩm Liên Quan
                    </h3>
                </div>

                <!-- Slide2 -->
                <div class="wrap-slick2">
                    <div class="slick2">
                        <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
                                <!-- Block2 -->
                                <div class="block2">
                                    <div class="block2-pic hov-img0">
                                        <img src="<?php echo e($relatedProduct->default_image_url); ?>"
                                            alt="<?php echo e($relatedProduct->name); ?>">

                                        <a href="<?php echo e(route('client.products.show', $relatedProduct->id)); ?>"
                                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                            Xem Chi Tiết
                                        </a>
                                    </div>

                                    <div class="block2-txt flex-w flex-t p-t-14">
                                        <div class="block2-txt-child1 flex-col-l ">
                                            <a href="<?php echo e(route('client.products.show', $relatedProduct->id)); ?>"
                                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                                <?php echo e($relatedProduct->name); ?>

                                            </a>

                                            <span class="stext-105 cl3">
                                                <?php if($relatedProduct->price_sale && $relatedProduct->price_sale < $relatedProduct->price): ?>
                                                    <span
                                                        class="fw-bold"><?php echo e(number_format($relatedProduct->price_sale, 0, ',', '.')); ?>đ</span>
                                                    <span
                                                        style="text-decoration: line-through; color: red;"><?php echo e(number_format($relatedProduct->price, 0, ',', '.')); ?>đ</span>
                                                <?php else: ?>
                                                    <?php echo e(number_format($relatedProduct->price, 0, ',', '.')); ?>đ
                                                <?php endif; ?>
                                            </span>
                                        </div>

                                        <div class="block2-txt-child2 flex-r p-t-3">
                                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                                <img class="icon-heart1 dis-block trans-04"
                                                    src="<?php echo e(asset('client/images/icons/icon-heart-01.png')); ?>"
                                                    alt="ICON">
                                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                                    src="<?php echo e(asset('client/images/icons/icon-heart-02.png')); ?>"
                                                    alt="ICON">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Lấy dữ liệu từ data attributes
            const productContainer = $('[data-product-id]');
            const variants = JSON.parse(productContainer.data('variants') || '[]');
            const originalPrice = parseFloat(productContainer.data('original-price') || 0);
            const originalPriceSale = parseFloat(productContainer.data('original-price-sale') || 0);
            const hasPriceSale = originalPriceSale && originalPriceSale < originalPrice;

            // Function để tìm variant dựa trên size, color (texture được tự động lấy)
            function findVariant(size, color) {
                const texture = $('#texture-select').val();

                return variants.find(variant => {
                    const vSize = variant.size ? variant.size.name : null;
                    const vColor = variant.color ? variant.color.name : null;
                    const vTexture = variant.texture ? variant.texture.name : null;

                    const okSize = !size || size === vSize;
                    const okColor = !color || color === vColor;
                    const okTexture = !texture || texture === vTexture;
                    return okSize && okColor && okTexture;
                });
            }

            // Function để cập nhật giá khi chọn variant
            function updatePrice() {
                const size = $('#size-select').val();
                const color = $('#color-select').val();
                const texture = $('#texture-select').val(); // Texture không thay đổi, giữ nguyên từ database

                const variant = findVariant(size, color);

                if (variant) {
                    // Luôn lưu variant_id kể cả khi giá = 0 (fallback về giá product)
                    $('input[name="variant_id"]').val(variant.id);

                    if (variant.price && parseFloat(variant.price) > 0) {
                        $('.mtext-106').html('<span class="fw-bold">' +
                            new Intl.NumberFormat('vi-VN').format(variant.price) + 'đ</span>');
                    } else {
                        if (hasPriceSale) {
                            $('.mtext-106').html('<span class="fw-bold">' +
                                new Intl.NumberFormat('vi-VN').format(originalPriceSale) + 'đ</span>' +
                                '<span style="text-decoration: line-through; color: red;">' +
                                new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ</span>');
                        } else {
                            $('.mtext-106').html(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
                        }
                    }
                } else {
                    // Không khớp biến thể nào -> reset giá và xoá variant_id
                    if (hasPriceSale) {
                        $('.mtext-106').html('<span class="fw-bold">' +
                            new Intl.NumberFormat('vi-VN').format(originalPriceSale) + 'đ</span>' +
                            '<span style="text-decoration: line-through; color: red;">' +
                            new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ</span>');
                    } else {
                        $('.mtext-106').html(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
                    }
                    $('input[name="variant_id"]').val('');
                }
            }

            // Đồng bộ hidden fields và variant khi mở trang (trường hợp đã có sẵn lựa chọn)
            (function syncInitial() {
                // Texture luôn được set từ đầu
                const texture = $('#texture-select').val();
                $('input[name="texture_name"]').val(texture || '');

                $('input[name="size_name"]').val($('#size-select').val() || '');
                $('input[name="color_name"]').val($('#color-select').val() || '');
                updatePrice();
            })();

            // Event listeners cho các dropdown (chỉ size và color)
            $('#size-select, #color-select').on('change', function() {
                // update hidden attribute names
                $('input[name="size_name"]').val($('#size-select').val() || '');
                $('input[name="color_name"]').val($('#color-select').val() || '');
                // Texture giữ nguyên từ database (không thay đổi)
                const texture = $('#texture-select').val();
                $('input[name="texture_name"]').val(texture || '');
                updatePrice();
            });

            // Quantity controls
            $('.btn-num-product-down').on('click', function() {
                const input = $(this).siblings('.num-product');
                const currentValue = parseInt(input.val());
                if (currentValue > 1) {
                    input.val(currentValue - 1);
                }
            });

            $('.btn-num-product-up').on('click', function() {
                const input = $(this).siblings('.num-product');
                const currentValue = parseInt(input.val());
                input.val(currentValue + 1);
            });

            // Chặn submit nếu có biến thể mà chưa chọn -> tránh lưu null
            $('#add-to-cart-form').off('submit').on('submit', function(e) {
                const variantId = $('input[name="variant_id"]').val();
                if (variants && variants.length > 0 && !variantId) {
                    e.preventDefault();
                    alert('Vui lòng chọn đầy đủ thông tin sản phẩm (kích thước, màu sắc, chất liệu)');
                    return false;
                }
                return true;
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/client/product/detail.blade.php ENDPATH**/ ?>