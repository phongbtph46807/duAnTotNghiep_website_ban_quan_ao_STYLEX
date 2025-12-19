@extends('admin.layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông báo</h1>
        @if ($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="type" value="{{ request('type') }}">
                <button type="submit" class="btn btn-sm btn-primary">Đánh dấu tất cả là đã đọc</button>
            </form>
        @endif
    </div>

    <div class="mb-3">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">
            Tất cả ({{ $typeCounts->sum() }})
        </a>
        <a href="{{ route('admin.notifications.index', ['type' => 'low_stock']) }}" class="btn btn-sm {{ request('type') == 'low_stock' ? 'btn-danger' : 'btn-outline-danger' }}">
            Tồn kho thấp ({{ $typeCounts['low_stock'] ?? 0 }})
        </a>
        <a href="{{ route('admin.notifications.index', ['type' => 'pending_approval']) }}" class="btn btn-sm {{ request('type') == 'pending_approval' ? 'btn-warning' : 'btn-outline-warning' }}">
            Chờ duyệt ({{ $typeCounts['pending_approval'] ?? 0 }})
        </a>
        <a href="{{ route('admin.notifications.index', ['type' => 'qc_failed']) }}" class="btn btn-sm {{ request('type') == 'qc_failed' ? 'btn-danger' : 'btn-outline-danger' }}">
            QC Failed ({{ $typeCounts['qc_failed'] ?? 0 }})
        </a>
        <a href="{{ route('admin.notifications.index', ['type' => 'count_discrepancy']) }}" class="btn btn-sm {{ request('type') == 'count_discrepancy' ? 'btn-warning' : 'btn-outline-warning' }}">
            Chênh lệch kiểm kê ({{ $typeCounts['count_discrepancy'] ?? 0 }})
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if ($notifications->isEmpty())
                <div class="alert alert-info">Không có thông báo nào.</div>
            @else
                <div class="list-group">
                    @foreach ($notifications as $notif)
                        <div class="list-group-item {{ is_null($notif->read_at) ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        @php
                                            $badgeColor = match($notif->type) {
                                                'low_stock', 'insufficient_stock', 'qc_failed' => 'danger',
                                                'pending_approval', 'count_discrepancy' => 'warning',
                                                'new_order', 'defect_found' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">{{ $notif->type }}</span>
                                        {{ $notif->title }}
                                    </h6>
                                    <p class="mb-1">{{ $notif->message }}</p>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                @if (is_null($notif->read_at))
                                    <form action="{{ route('admin.notifications.mark-read', $notif->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Đánh dấu đã đọc</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
