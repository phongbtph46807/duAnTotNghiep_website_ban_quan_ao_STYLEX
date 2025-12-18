@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-money"></i> Quản Lý Lương - Tháng {{ $month }}/{{ $year }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.salaries.create') }}" class="btn btn-success btn-sm">
                <i class="bx bx-plus"></i> Tạo Lương
            </a>
            <a href="{{ route('admin.salaries.generate-by-role') }}" class="btn btn-info btn-sm">
                <i class="bx bx-cog"></i> Tạo Tự Động
            </a>
            <a href="{{ route('admin.salaries.history') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-history"></i> Lịch Sử
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-x-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 mt-1"></i>
            <div>
                <strong>Quy trình Duyệt Lương:</strong>
                <ol class="mb-0 mt-2 ps-3 small">
                    <li>Tạo lương: Nhập thông tin lương cho nhân viên hoặc tạo tự động theo role</li>
                    <li>Chờ duyệt: Lương ở trạng thái "pending" - Admin có thể duyệt hoặc từ chối</li>
                    <li>Duyệt/Từ chối: Click "Duyệt" để phê duyệt hoặc "Từ Chối" với lý do cụ thể</li>
                    <li>Hoàn thành: Lương đã được duyệt sẽ chuyển sang trạng thái "approved"</li>
                </ol>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Danh Sách Lương ({{ $salaries->count() }})</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nhân Viên</th>
                            <th class="text-end">Lương Cơ Bản</th>
                            <th class="text-end">Thưởng</th>
                            <th class="text-end">Khấu Trừ</th>
                            <th class="text-end">Tổng Lương</th>
                            <th class="text-center">Trạng Thái</th>
                            <th>Người Duyệt</th>
                            <th class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salaries as $salary)
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
                                <td class="text-end">
                                    @if($salary->status === 'pending')
                                        <button onclick="approveSalary({{ $salary->id }})" class="btn btn-sm btn-success" title="Duyệt Lương">
                                            <i class="bx bx-check"></i>
                                        </button>
                                        <button onclick="showRejectModal({{ $salary->id }})" class="btn btn-sm btn-danger" title="Từ Chối">
                                            <i class="bx bx-x"></i>
                                        </button>
                                        <a href="{{ route('admin.salaries.edit', $salary) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    @elseif($salary->status === 'approved')
                                        <span class="badge bg-secondary">Hoàn Thành</span>
                                    @else
                                        <button onclick="showRejectionReason('{{ addslashes($salary->rejection_reason) }}')" class="btn btn-sm btn-outline-danger" title="Xem Lý Do">
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-inbox fs-1"></i>
                                        <p class="mt-2">Chưa có dữ liệu lương</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-x-circle text-danger"></i> Từ Chối Lương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle"></i> 
                        <strong>Lưu ý:</strong> Lý do từ chối phải có ít nhất 10 ký tự và sẽ được ghi lại trong hệ thống.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required 
                                  placeholder="Nhập lý do từ chối lương (tối thiểu 10 ký tự)..."
                                  minlength="10" maxlength="500"></textarea>
                        <div class="form-text">Tối thiểu 10 ký tự, tối đa 500 ký tự</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-check"></i> Xác Nhận Từ Chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveSalary(id) {
    if(confirm('Xác nhận phê duyệt lương này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/salaries/' + id + '/approve';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        document.body.appendChild(form);
        form.submit();
    }
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/salaries/' + id + '/reject';
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
    
    // Clear previous input
    document.querySelector('#rejectModal textarea[name="rejection_reason"]').value = '';
}

function showRejectionReason(reason) {
    alert('Lý do từ chối: ' + reason);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const reason = this.querySelector('textarea[name="rejection_reason"]').value.trim();
            if (reason.length < 10) {
                e.preventDefault();
                alert('Lý do từ chối phải có ít nhất 10 ký tự');
                return false;
            }
        });
    }
});
</script>

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