@extends('admin.layouts.app')
@section('title', 'Cập nhật sản phẩm')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lí sản phẩm</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Quản lí sản phẩm</a></li>
                        <li class="breadcrumb-item active">Cập nhật sản phẩm</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Cập nhật sản phẩm: {{ $product->name }}</h4>
        </div><!-- end card header -->
        <div class="card-body">
            <form id="product-form" method="POST" action="{{ route('admin.products.update', $product->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card" style="border-width: 2px;">
                            <div class="card-header" style="background-color:aliceblue">
                                <h5 class="card-title mb-0">Thông tin chung</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="product-title-input">Tên sản phẩm</label>
                                    <input type="text" class="form-control" name="name" id="product-title-input"
                                        value="{{ old('name', $product->name) }}" placeholder="Nhập tên sản phẩm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="product-title-input">Tiêu đề SEO</label>
                                    <input type="text" class="form-control" name="meta_title"
                                        value="{{ old('meta_title', $product->meta_title) }}"
                                        placeholder="Nhập tiêu đề SEO">
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="col-6 pe-2">
                                        <label for="choices-publish-status-input" class="form-label">Trạng thái</label>

                                        <select class="form-select" id="choices-publish-status-input" name="status">
                                            <option value="active"
                                                {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="">Danh mục</label>
                                        <select class="form-select" id="choices-category-input" name="category_id">
                                            <option value="#" selected>Chọn danh mục</option>
                                            @foreach ($categories as $cate)
                                                <option value="{{ $cate->id }}"
                                                    {{ old('category_id', $product->category_id) == $cate->id ? 'selected' : '' }}>
                                                    {{ $cate->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                    <div class="col-lg-5">
                        <div class="card" style="border-width: 2px;">
                            <div class="card-header d-flex align-items-center justify-content-between"
                                style="background-color:aliceblue">
                                <h5 class="card-title mb-0">Sản phẩm nổi bật</h5>
                                <div class="form-check form-switch form-switch-warning">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured"
                                        value="1" @checked($product->is_featured)>
                                </div>
                            </div>

                        </div>
                        <div class="card" style="border-width: 2px;">
                            <div class="card-header" style="background-color:aliceblue">
                                <h5 class="card-title mb-0">Ảnh sản phẩm</h5>
                            </div>
                            <div class="card-body" style="height: 193px">
                                <div class="mb-4">
                                    <h5 class="fs-14 mb-1">Ảnh chính</h5>
                                        <div class="text-center position-relative" style="width: 100px; height: 100px;">
                                            <!-- Ảnh cũ -->
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}" id="old-image"
                                                class="img-fluid rounded"
                                                style="width: 100px; height: 100px; object-fit: cover;position:absolute; top:0 ;left:170px">

                                            <!-- Ảnh preview mới, ban đầu ẩn -->
                                            <img id="preview-image" src=""
                                                class="img-fluid rounded"
                                                style="width: 100px; height: 100px; object-fit: cover; opacity: 0; transition: opacity 0.3s;position:absolute; top:0 ;left:170px">

                                            <!-- Nút chọn file tùy chỉnh -->
                                            <label for="product-image-input"
                                                class="btn btn-sm position-absolute m-1"
                                                style="cursor: pointer;position:absolute;left:260px;bottom:-15px;">
                                                <i class="ri-image-fill me-1"></i>
                                            </label>

                                            <input type="file" name="thumbnail" id="product-image-input"
                                                accept="image/*" style="display: none;">
                                        </div>


                                        <!-- Nút chọn file tùy chỉnh -->
                                        <label for="product-image-input" class="btn btn-sm"
                                            style="cursor: pointer; margin-top:150px">
                                            <i class="ri-image-fill me-1"></i>
                                        </label>

                                        <!-- input file ẩn -->
                                        <input type="file" name="thumbnail" id="product-image-input" accept="image/*"
                                            style="display: none;" />
                                    </div>
                            </div>

                        </div>
                    </div>
                    <!-- end col -->
                    <div>
                        <div class="card" style="border-width: 2px;">
                            <div class="card-header" style="background-color:aliceblue">
                                <h5 class="card-title mb-0">Mô tả sản phẩm</h5>
                            </div>
                            <div class="card-body">
                                <textarea name="description" id="description" class="form-control">
                                        {{ old('description', $product->description) }}
                                    </textarea>
                                <textarea name="description" id="description" hidden></textarea>
                            </div>
                            <!-- end card body -->
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Submit</button>
                    {{-- <button type="button" onclick="console.log(new FormData(this.form));">Test Form</button> --}}
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        @once
        let myEditor;
        ClassicEditor.create(document.querySelector('#description'))
            .then(editor => {
                myEditor = editor;
            });

        document.querySelector('#product-form').addEventListener('submit', function() {
            document.querySelector('#description').value = myEditor.getData();
        });
        @endonce
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("product-image-input");
            const preview = document.getElementById("preview-image");

            input.addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.opacity = 1; // hiện ảnh preview lên trên
                    };

                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.opacity = 0; // ẩn ảnh preview nếu bỏ chọn
                }
            });
        });
    </script>
@endpush
