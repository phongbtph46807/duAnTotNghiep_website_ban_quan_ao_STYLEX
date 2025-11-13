@extends('client.layout.layout')
@section('title', 'Lịch sử quay thưởng')

@section('content')
    <div class="container my-5">
        <h2 class="mb-4">📜 Lịch sử quay thưởng của bạn</h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Phần thưởng</th>
                            <th>Voucher</th>
                            <th>Trạng thái</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($userSpins as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $item->spin->name }}</strong></td>
                                <td>
                                    @if($item->spin->voucher)
                                        <span class="badge bg-primary">{{ $item->spin->voucher->code }}</span>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_claimed)
                                        <span class="badge bg-success">Đã nhận</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa nhận</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có lịch sử quay</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $userSpins->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
