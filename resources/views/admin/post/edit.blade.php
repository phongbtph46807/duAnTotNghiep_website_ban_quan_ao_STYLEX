@extends('admin.layouts.app')
@section('content')
    <form action="{{ route('admin.post.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ old('title', $post->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug', $post->slug) }}">
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea id="content" name="content" class="form-control" rows="6">{{ old('content', $post->content) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mb-4">
                <a href="{{ route('admin.post.index') }}" class="btn btn-secondary w-sm">Back</a>
                <button type="submit" class="btn btn-success w-sm">Update</button>
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
