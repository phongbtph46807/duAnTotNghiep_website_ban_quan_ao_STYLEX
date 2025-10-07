@extends('layouts.admin-layout')
@section('content')
<div class="page-title">
    <h1>Danh mục</h1>
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <nav aria-label="breadcrumb" class="breadcrumb-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh mục</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <div class="row" id="table-striped">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        Thêm Danh Mục
                    </button>
                    <!-- form add -->
                    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCategoryModalLabel">
                                        <i class="bi bi-plus-circle-fill me-2"></i> Thêm Danh Mục Mới
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addCategoryForm" class="p-2">
                                        @csrf
                                        <!-- Tên danh mục -->
                                        <div class="mb-4">
                                            <label for="categoryName" class="form-label fw-semibold">
                                                <i class="bi bi-tag-fill me-2 text-primary"></i> Tên danh mục
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control border-0 shadow-sm theme-input"
                                                name="category_name"
                                                placeholder="Nhập tên danh mục">
                                        </div>

                                        <!-- Danh mục cha -->
                                        <div class="mb-3">
                                            <label for="parentCategory" class="form-label fw-semibold">
                                                <i class="bi bi-diagram-3-fill me-2 text-success"></i> Danh mục cha
                                            </label>
                                            <select
                                                class="form-select border-0 shadow-sm theme-input"
                                                name="parent_id">
                                                <option selected value="">Không có (Mặc định)</option>
                                                @foreach ($allCategories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Nút hành động -->
                                        <div class="text-end mt-4">
                                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle"></i> Hủy
                                            </button>
                                            <button type="submit" class="btn btn-primary px-4 addBtn">
                                                <i class="bi bi-plus-circle"></i> Thêm danh mục
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-content">
                    <section class="section">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive datatable-minimal">
                                    <table class="table table-hover table-lg ">
                                        <thead>
                                            <tr>
                                                <th>Danh Mục</th>
                                                <th>Danh Mục Cha</th>
                                                <th>Trạng Thái</th>
                                                <th>Thao Tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($allCategories as $category)
                                                @if ($category->parent_id === null)
                                                    <tr class=" parent-row" data-parent-id="{{ $category->id }}" style="cursor:pointer;">
                                                        <td><strong><i class="bi bi-caret-right-fill toggle-icon me-2"></i>{{ $category->name }}</strong></td>
                                                        <td>—</td>
                                                        <td>
                                                            @if($category->status == 1)
                                                                <span class="badge bg-success">Hoạt động</span>
                                                            @else
                                                                <span class="badge bg-secondary">Không hoạt động</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="#" data-id="{{ $category->id }}"><i data-feather="edit"></i></a>
                                                            <a href="#" data-id="{{ $category->id }}"><i data-feather="trash-2"></i></a>
                                                        </td>
                                                    </tr>

                                                    {{-- Các danh mục con --}}
                                                    @foreach ($category->children as $child)
                                                        <tr class="child-row child-of-{{ $category->id }}" style="display:none;">
                                                            <td style="padding-left: 40px;">↳ {{ $child->name }}</td>
                                                            <td>{{ $category->name }}</td>
                                                            <td>
                                                                @if($child->status == 1)
                                                                    <span class="badge bg-success">Hoạt động</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Không hoạt động</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="#" data-id="{{ $child->id }}"><i data-feather="edit"></i></a>
                                                                <a href="#" data-id="{{ $child->id }}"><i data-feather="trash-2"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                    {{ $allCategories->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Thêm danh mục
        $('#addCategoryForm').submit(function(e) {
            e.preventDefault();
            $('.addBtn').prop('disabled', true);
            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.category.store') }}",
                type: "POST",
                data: formData,
                success: function(res) {
                    alert(res.msg);
                    $('.addBtn').prop('disabled', false);
                    if (res.success) {
                        location.reload();
                    }
                }
            });
        });

        // Ẩn/hiện danh mục con
        $(document).on('click', '.parent-row', function(e) {
            if ($(e.target).closest('a').length) return;
            const parentId = $(this).data('parent-id');
            const children = $(`.child-of-${parentId}`);
            const icon = $(this).find('.toggle-icon');

            if (children.is(':visible')) {
                children.hide();
                icon.removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
            } else {
                children.show();
                icon.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
            }
        });
    });
</script>
@endpush


