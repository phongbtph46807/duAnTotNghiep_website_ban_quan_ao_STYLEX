@extends('client.layout.layout')
@section('title', 'Bài Viết - ' . env('APP_NAME'))
@section('content')
    {{-- Sử dụng helper của Laravel để rút gọn nội dung --}}
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-12 text-center mb-4">
                <h1 class="display-5">Tin tức & Bài viết</h1>
                <p class="lead">Cập nhật các thông tin và bài viết mới nhất từ chúng tôi.</p>
            </div>
        </div>

        <div class="row g-4">
            @if($posts->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        Hiện chưa có bài viết nào.
                    </div>
                </div>
            @else
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                        <div class="card shadow-sm border-0 rounded-3 w-100 position-relative">

                            @if ($post->image)
                                <img src="{{ $post->image }}"
                                     class="card-img-top"
                                     alt="{{ $post->title }}"
                                     style="object-fit: cover; height: 220px;"
                                     onerror="this.src='https://placehold.co/600x400/EBF2FA/7F8A9A?text=H%C3%ACnh+%E1%BA%A3nh+l%E1%BB%97i'; this.onerror=null;">
                            @else
                                <div class="d-flex align-items-center justify-content-center"
                                     style="height: 220px; background-color: #f8f9fa;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#adb5bd" class="bi bi-newspaper" viewBox="0 0 16 16">
                                        <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.11.995l-.35.753-2.336-4.31A1.5 1.5 0 0 0 9.15 8.98l-1.622 1.956-1.39-2.78a1.5 1.5 0 0 0-1.387-.995L.11 14.249a1.5 1.5 0 0 1-.11-.995V2.5zM1.5 2a.5.5 0 0 0-.5.5v11.5a.5.5 0 0 0 .017.07l.006.014 3.68-1.55L7.29 9.82a.5.5 0 0 1 .494.007l1.35 2.7 1.12-1.31a.5.5 0 0 1 .494-.007l2.35 4.227.005.008.007.004a.5.5 0 0 0 .03.001.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-.5-.5h-11z"/>
                                        <path d="M3 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zM3 5.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zM3 7.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('client.posts.show', $post->slug) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $post->title }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted small flex-grow-1">
                                    {{ Str::limit(strip_tags($post->content), 100, '...') }}
                                </p>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        @if ($post->author)
                                            Bởi: <strong>{{ $post->author->name }}</strong>
                                        @else
                                            Bởi: <strong>Vô danh</strong>
                                        @endif
                                    </small>
                                    <small class="text-muted">
                                        {{ $post->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

