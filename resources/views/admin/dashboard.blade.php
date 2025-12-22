@extends('admin.layouts.app')
@section('title', 'Dashboard - Style X Admin')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Chào mừng, {{ Auth::user()->name }}!</h4>
                    <p class="text-muted mb-0">Dashboard tổng quan kinh doanh</p>
                </div>
                <form method="GET" class="d-flex gap-2">
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 150px;">
                        <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 ngày</option>
                        <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 ngày</option>
                        <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 ngày</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="{{ $alert['icon'] }} me-2"></i>
                <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- KPI Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Tổng doanh thu</p>
                            <h5 class="mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h5>
                            <small class="text-success">{{ $totalOrders }} đơn</small>
                            @if($revenueGrowth >= 0)
                                <div class="text-success small mt-1"><i class="ri-arrow-up-line"></i> {{ $revenueGrowth }}%</div>
                            @else
                                <div class="text-danger small mt-1"><i class="ri-arrow-down-line"></i> {{ $revenueGrowth }}%</div>
                            @endif
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-circle p-2">
                            <i class="ri-money-dollar-circle-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Lợi nhuận</p>
                            <h5 class="mb-0">{{ number_format($totalProfit, 0, ',', '.') }} ₫</h5>
                            <small class="text-info">{{ $profitMargin }}% margin</small>
                            @if($profitGrowth >= 0)
                                <div class="text-success small mt-1"><i class="ri-arrow-up-line"></i> {{ $profitGrowth }}%</div>
                            @else
                                <div class="text-danger small mt-1"><i class="ri-arrow-down-line"></i> {{ $profitGrowth }}%</div>
                            @endif
                        </div>
                        <span class="badge bg-info-subtle text-info rounded-circle p-2">
                            <i class="ri-profit-2-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Đơn chờ xử lý</p>
                            <h5 class="mb-0">{{ $pendingOrders }}</h5>
                            <small class="text-warning">Cần xác nhận</small>
                        </div>
                        <span class="badge bg-warning-subtle text-warning rounded-circle p-2">
                            <i class="ri-time-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Hàng sắp hết</p>
                            <h5 class="mb-0">{{ $lowStockCount }}</h5>
                            <small class="text-danger">Cần bổ sung</small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger rounded-circle p-2">
                            <i class="ri-alert-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Doanh thu theo ngày</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Trạng thái đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Chờ xử lý</span>
                        <span class="fw-semibold">{{ $pendingOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Đang xử lý</span>
                        <span class="fw-semibold">{{ $processingOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Đang giao</span>
                        <span class="fw-semibold">{{ $shippingOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hoàn thành</span>
                        <span class="fw-semibold text-success">{{ $completedOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Hủy/Trả</span>
                        <span class="fw-semibold text-danger">{{ $cancelledOrders + $returnedOrders }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Row -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Top 5 sản phẩm bán chạy</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductsBySales as $product)
                                <tr>
                                    <td>{{ Str::limit($product->name, 25) }}</td>
                                    <td class="text-end">{{ $product->total_qty }}</td>
                                    <td class="text-end">{{ number_format($product->total_revenue, 0, ',', '.') }} ₫</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Top 5 sản phẩm lợi nhuận cao</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Lợi nhuận</th>
                                    <th class="text-end">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductsByProfit as $product)
                                <tr>
                                    <td>{{ Str::limit($product->name, 25) }}</td>
                                    <td class="text-end">{{ number_format($product->profit, 0, ',', '.') }} ₫</td>
                                    <td class="text-end">{{ round(($product->profit / max($product->total_revenue, 1) * 100), 1) }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng khách</span>
                        <span class="fw-semibold">{{ $uniqueCustomers }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Đã đăng ký</span>
                        <span class="fw-semibold">{{ $registeredCustomers }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Khách vãng lai</span>
                        <span class="fw-semibold">{{ $guestCustomers }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Đã thanh toán</span>
                        <span class="fw-semibold">{{ $paidOrders }}/{{ $totalOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tỷ lệ</span>
                        <span class="fw-semibold">{{ round(($paidOrders / max($totalOrders, 1) * 100), 1) }}%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Chưa thanh toán</span>
                        <span class="fw-semibold text-warning">{{ $unpaidOrders }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Tồn kho</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giá trị</span>
                        <span class="fw-semibold">{{ number_format($inventoryValue, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số lượng</span>
                        <span class="fw-semibold">{{ $totalOnHand }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Sắp hết</span>
                        <span class="fw-semibold text-danger">{{ $lowStockCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($dailySalesTrend);

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesData.map(d => new Date(d.date).toLocaleDateString('vi-VN')),
            datasets: [{
                label: 'Doanh thu',
                data: salesData.map(d => d.revenue),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
