@extends('admin.layouts.app')
@section('title', 'Báo cáo Phân tích Tổng')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Báo cáo Phân tích Tổng</h4>
        </div>
    </div>
</div>

{{-- 1. Báo cáo Tồn kho Âm --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0 text-white">⚠️ Báo cáo Tồn kho Âm (Negative Stock)</h5>
            </div>
            <div class="card-body">
                @if($negativeStock->count() > 0)
                <div class="alert alert-danger">
                    <strong>Phát hiện {{ $negativeStock->count() }} sản phẩm có tồn kho âm!</strong>
                    <p class="mb-0">Đây là lỗi nghiêm trọng cần xử lý ngay lập tức.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng âm</th>
                                <th>Giá trị vốn bị âm</th>
                                <th>Thời điểm phát sinh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($negativeStock as $item)
                            <tr>
                                <td><code>{{ $item->sku }}</code></td>
                                <td>{{ $item->name }}</td>
                                <td class="text-danger fw-bold">{{ $item->total_stock }}</td>
                                <td class="text-danger">{{ number_format($item->negative_value) }}₫</td>
                                <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i> Không có sản phẩm nào bị tồn kho âm.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 2. Hiệu suất & Tốc độ --}}
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="ri-speed-line text-info fs-1"></i>
                <h6 class="text-muted mt-2">Tốc độ xử lý trung bình</h6>
                <h4 class="text-info">{{ $performance['avg_processing_time'] }}s</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="ri-flashlight-line text-success fs-1"></i>
                <h6 class="text-muted mt-2">Tỷ lệ xử lý tức thời</h6>
                <h4 class="text-success">{{ $performance['realtime_rate'] }}%</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="ri-shield-check-line text-primary fs-1"></i>
                <h6 class="text-muted mt-2">Tỷ lệ thành công</h6>
                <h4 class="text-primary">{{ $failedTransactions['success_rate'] }}%</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- 3. Lỗi COGS --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">⚠️ Lỗi Tính Giá Vốn (COGS)</h5>
            </div>
            <div class="card-body">
                @if($cogsErrors->count() > 0)
                <div class="alert alert-warning">
                    <strong>{{ $cogsErrors->count() }} giao dịch có lỗi COGS</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SKU</th>
                                <th>Số lượng</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cogsErrors->take(10) as $error)
                            <tr>
                                <td>{{ $error->id }}</td>
                                <td><code>{{ $error->sku }}</code></td>
                                <td>{{ $error->quantity }}</td>
                                <td>{{ \Carbon\Carbon::parse($error->created_at)->format('d/m H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i> Không có lỗi COGS.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 4. Phân tích Batch --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">📊 Phân tích Lô hàng</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-muted">Tỷ lệ FIFO</h6>
                        <h4 class="text-success">{{ $batchAnalysis['fifo_usage_rate'] }}%</h4>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Độ sâu Batch TB</h6>
                        <h4 class="text-info">{{ round($batchAnalysis['batch_depth_avg'], 1) }}</h4>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-muted">Lô đã hết</h6>
                        <h4 class="text-secondary">{{ $batchAnalysis['empty_batches'] }}</h4>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Lô một phần</h6>
                        <h4 class="text-warning">{{ $batchAnalysis['partial_batches'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. Nhật ký Kiểm toán --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">📋 Nhật ký Giao dịch Xuất (100 gần nhất)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Số lượng</th>
                                <th>Batch</th>
                                <th>COGS</th>
                                <th>Ref ID</th>
                                <th>Người thực hiện</th>
                                <th>Thời gian</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditTrail->take(20) as $log)
                            <tr>
                                <td><code>{{ $log->sku }}</code></td>
                                <td>{{ $log->quantity }}</td>
                                <td>{{ $log->batch_number }}</td>
                                <td>{{ $log->unit_cost ? number_format($log->unit_cost) . '₫' : '-' }}</td>
                                <td>{{ $log->reference_id ?? '-' }}</td>
                                <td>{{ $log->user_name ?? 'System' }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m H:i') }}</td>
                                <td>{{ Str::limit($log->notes, 30) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection