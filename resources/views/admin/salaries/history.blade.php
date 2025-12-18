@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-history"></i> Lịch Sử Lương</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Lịch Sử Lương ({{ $salaries->total() }})</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nhân Viên</th>
                            <th class="text-center">Tháng/Năm</th>
                            <th class="text-end">Lương Cơ Bản</th>
                            <th class="text-end">Thưởng</th>
                            <th class="text-end">Khấu Trừ</th>
                            <th class="text-end">Tổng Lương</th>
                            <th class="text-center">Trạng Thái</th>
                            <th>Người Duyệt</th>
                            <th class="text-center">Ngày Tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                            {{ substr($salary->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $salary->user->name }}</h6>
                                        <small class="text-muted">{{ $salary->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $salary->month }}/{{ $salary->year }}</span>
                            </td>
                            <td class="text-end">{{ number_format($salary->base_salary) }} VND</td>
                            <td class="text-end">{{ number_format($salary->bonus) }} VND</td>
                            <td class="text-end">{{ number_format($salary->deduction) }} VND</td>
                            <td class="text-end"><strong>{{ number_format($salary->getTotalSalary()) }} VND</strong></td>
                            <td class="text-center">
                                @if($salary->status === 'pending')
                                    <span class="badge bg-warning">Chờ Duyệt</span>
                                @elseif($salary->status === 'approved')
                                    <span class="badge bg-success">Đã Duyệt</span>
                                @else
                                    <span class="badge bg-danger">Bị Từ Chối</span>
                                @endif
                            </td>
                            <td>
                                @if($salary->status === 'approved' && $salary->approvedBy)
                                    <small class="text-success">
                                        <i class="bx bx-check"></i> {{ $salary->approvedBy->name }}<br>
                                        <span class="text-muted">{{ $salary->approved_at->format('d/m/Y H:i') }}</span>
                                    </small>
                                @elseif($salary->status === 'rejected' && $salary->rejectedBy)
                                    <small class="text-danger">
                                        <i class="bx bx-x"></i> {{ $salary->rejectedBy->name }}<br>
                                        <span class="text-muted">{{ $salary->rejected_at->format('d/m/Y H:i') }}</span>
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ $salary->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-inbox fs-1"></i>
                                    <p class="mt-2">Chưa có dữ liệu lịch sử</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($salaries->hasPages())
        <div class="card-footer">
            {{ $salaries->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.avatar-sm {
    height: 2rem;
    width: 2rem;
}
.avatar-title {
    align-items: center;
    display: flex;
    font-size: 0.875rem;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}
</style>
@endsection