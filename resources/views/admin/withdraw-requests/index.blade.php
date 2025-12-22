@extends('admin.layouts.app')
@section('title', 'Quản lý yêu cầu rút tiền')

@push('page-css')
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
<style>
    .stat-card {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        padding: 18px 22px;
        height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all .25s ease;
        background: #fff;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 30px -15px rgba(15, 23, 42, 0.4);
    }
    .stat-card .stat-label {
        font-size: 13px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
    }
    .stat-trend {
        font-size: 13px;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-approved {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }
    .bank-info {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        margin-top: 8px;
    }
    .bank-info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 13px;
    }
    .bank-info-item:last-child {
        margin-bottom: 0;
    }
    .bank-info-label {
        color: #64748b;
        font-weight: 500;
    }
    .bank-info-value {
        color: #0f172a;
        font-weight: 600;
    }
    .withdraw-table thead th {
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: none;
    }
    .withdraw-table tbody td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-wallet-3-line me-2"></i>Quản lý yêu cầu rút tiền</h4>
            </div>
        </div>
    </div>

    {{-- Thống kê --}}
    <div class="row cursor-pointer">
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Chờ duyệt</span>
                <span class="stat-value text-warning">{{ number_format($stats['pending']) }}</span>
                <span class="stat-trend text-muted">Tổng: {{ number_format($stats['total_amount_pending']) }} ₫</span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Đã duyệt</span>
                <span class="stat-value text-info">{{ number_format($stats['approved']) }}</span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Từ chối</span>
                <span class="stat-value text-danger">{{ number_format($stats['rejected']) }}</span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-white">
                <span class="stat-label">Hoàn thành</span>
                <span class="stat-value text-success">{{ number_format($stats['completed']) }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.withdraw-requests.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tìm kiếm (Tên/Email)</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="Nhập tên hoặc email người dùng">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle withdraw-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Số tiền</th>
                                    <th>Thông tin ngân hàng</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Người xử lý</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawRequests as $request)
                                    <tr>
                                        <td>#{{ $request->id }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $request->user->name }}</strong><br>
                                                <small class="text-muted">{{ $request->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong style="color: #ef4444; font-size: 16px;">
                                                {{ number_format($request->amount) }} ₫
                                            </strong>
                                        </td>
                                        <td>
                                            <div class="bank-info">
                                                <div class="bank-info-item">
                                                    <span class="bank-info-label">Ngân hàng:</span>
                                                    <span class="bank-info-value">{{ $request->bank_code }}</span>
                                                </div>
                                                <div class="bank-info-item">
                                                    <span class="bank-info-label">Số TK:</span>
                                                    <span class="bank-info-value">{{ $request->account_number }}</span>
                                                </div>
                                                <div class="bank-info-item">
                                                    <span class="bank-info-label">Chủ TK:</span>
                                                    <span class="bank-info-value">{{ $request->account_name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $request->note ?: '-' }}</small>
                                            @if($request->admin_note)
                                                <br><small class="text-muted">Admin: {{ $request->admin_note }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-status badge-{{ $request->status }}">
                                                {{ $request->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $request->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($request->processor)
                                                <div>{{ $request->processor->name }}</div>
                                                <small class="text-muted">{{ $request->processed_at ? $request->processed_at->format('d/m/Y H:i') : '' }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="submitApprove({{ $request->id }})">
                                                        Duyệt
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal"
                                                            data-request-id="{{ $request->id }}">
                                                        Từ chối
                                                    </button>
                                                </div>
                                            @elseif($request->status == 'approved')
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick="submitComplete({{ $request->id }})">
                                                    Hoàn thành
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <p class="text-muted mb-0">Không có yêu cầu rút tiền nào.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $withdrawRequests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Từ chối yêu cầu rút tiền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ri-alert-line me-2"></i>
                        Yêu cầu sẽ bị từ chối và không trừ tiền từ ví người dùng.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="admin_note" class="form-control" rows="3" 
                                  placeholder="Nhập lý do từ chối..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Đảm bảo functions được định nghĩa global
    window.submitApprove = function(id) {
        if (!confirm('Bạn có chắc chắn muốn duyệt yêu cầu rút tiền này?')) {
            return false;
        }
        
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/withdraw-requests/${id}/approve`;
            form.style.display = 'none';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            document.body.appendChild(form);
            form.submit();
            return false;
        } catch (e) {
            console.error('Error submitting approve form:', e);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            return false;
        }
    };
    
    window.submitComplete = function(id) {
        if (!confirm('Bạn đã chuyển tiền thành công cho người dùng?')) {
            return false;
        }
        
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/withdraw-requests/${id}/complete`;
            form.style.display = 'none';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            document.body.appendChild(form);
            form.submit();
            return false;
        } catch (e) {
            console.error('Error submitting complete form:', e);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            return false;
        }
    };
    
    // Setup reject modal
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('rejectModal');
        const rejectForm = document.getElementById('rejectForm');
        
        if (rejectModal && rejectForm) {
            rejectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const requestId = button ? button.getAttribute('data-request-id') : null;
                if (requestId) {
                    rejectForm.action = `/admin/withdraw-requests/${requestId}/reject`;
                }
            });
        }
    });
})();
</script>
@endpush


