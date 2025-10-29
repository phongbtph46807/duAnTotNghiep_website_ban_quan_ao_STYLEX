@extends('admin.layouts.app')
@section('content')
    <form action="{{ route('admin.post.store') }}" method="POST">
        @csrf
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter project title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" name="slug" id="slug" placeholder="Enter slug">
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea id="content" name="content" class="form-control" rows="6"></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mb-4">
                <button type="submit" class="btn btn-success w-sm">Create</button>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                            'blockQuote', 'undo', 'redo'
                        ]
                    },
                    language: 'vi'
                })
                .then(editor => {})
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
@endsection
