@extends('admin.layouts.app')
@section('title', 'Thêm mới sản phẩm')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lí sản phẩm</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Quản lí sản phẩm</a></li>
                        <li class="breadcrumb-item active">Thêm mới sản phẩm</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Thêm mới sản phẩm</h4>
        </div><!-- end card header -->
        <div class="card-body">
            <form id="product-form" method="POST" action="{{ route('admin.products.store') }}"
                enctype="multipart/form-data">
                @csrf
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
                                        value="{{ old('name') }}" placeholder="Nhập tên sản phẩm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="product-title-input">Tiêu đề SEO</label>
                                    <input type="text" class="form-control" name="meta_title"
                                        value="{{ old('meta_title') }}" placeholder="Nhập tiêu đề SEO">
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="col-6 pe-2">
                                        <label for="choices-publish-status-input" class="form-label">Trạng thái</label>

                                        <select class="form-select" id="choices-publish-status-input" name="status">
                                            <option value="#" selected>Chọn trạng thái</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="">Danh mục</label>
                                        <select class="form-select" id="choices-category-input" name="category_id">
                                            <option value="#" selected>Chọn danh mục</option>
                                            @foreach ($categories as $cate)
                                                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
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
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured" value="1">
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
                                    <div class="text-center">
                                        <div class="position-relative d-inline-block">
                                            <div class="position-absolute top-100 start-100 translate-middle">
                                                <label for="product-image-input" class="mb-0" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" title="Select Image">
                                                    <div class="avatar-xs">
                                                        <div
                                                            class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                                            <i class="ri-image-fill"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                                <input class="form-control d-none" value="" id="product-image-input"
                                                    type="file" name="thumbnail"
                                                    accept="image/png, image/gif, image/jpeg, image/webp">
                                            </div>
                                            <div class="avatar-lg">
                                                <div class="avatar-title bg-light rounded">
                                                    <img src="" id="product-img" class="avatar-md h-auto" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
            // preview image
            const input = document.getElementById("product-image-input");
            const preview = document.getElementById("product-img");

            input.addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result; // gán ảnh vào thẻ img
                    };

                    reader.readAsDataURL(file); // đọc file thành base64
                } else {
                    preview.src = ""; // nếu bỏ chọn thì reset preview
                }
            });
        });
    </script>
@endpush
