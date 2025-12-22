<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warehouse\WarehouseRequest ;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $query = Warehouse::query();

        // Tìm kiếm theo mã hoặc tên kho
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại kho
        if (request('type')) {
            $query->where('type', request('type'));
        }

        // Lọc theo trạng thái
        if (request('status')) {
            $query->where('operational_status', request('status'));
        }

        $warehouses = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.warehouse.index', compact('warehouses'));
    }

    public function show(Warehouse $warehouse)
    {
        // Load tồn kho với thông tin sản phẩm (10 sản phẩm đầu)
        $stocks = $warehouse->warehouseStocks()
            ->with(['variant.product'])
            ->orderBy('on_hand', 'desc')
            ->limit(10)
            ->get();

        // Tổng tồn kho
        $totalStock = $warehouse->warehouseStocks()->sum('on_hand');
        $totalProducts = $warehouse->warehouseStocks()->count();

        // Lịch sử giao dịch gần đây (10 giao dịch)
        $recentLogs = $warehouse->inventoryLogs()
            ->with(['variant.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.warehouse.show', compact('warehouse', 'stocks', 'totalStock', 'totalProducts', 'recentLogs'));
    }

    public function create()
    {
        return view('admin.warehouse.create');
    }

    public function store(WarehouseRequest $request)
    {
        Warehouse::create($request->validated());
        return redirect()->route('admin.inventory.warehouses.index')
                         ->with('success', 'Tạo kho hàng thành công.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouse.edit', compact('warehouse'));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $validated = $request->validated();
        $newStatus = $validated['operational_status'];
        $oldStatus = $warehouse->operational_status;

        // Kiểm tra điều kiện nghiệp vụ khi thay đổi trạng thái
        if ($oldStatus !== $newStatus) {
            // Nếu chuyển sang INACTIVE: Kiểm tra tồn kho
            if ($newStatus === 'INACTIVE') {
                $totalStock = $warehouse->warehouseStocks()->sum('on_hand');
                if ($totalStock > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Không thể chuyển kho sang trạng thái Tạm ngưng. Kho đang có {$totalStock} sản phẩm tồn kho. Vui lòng xử lý tồn kho trước.");
                }
            }

            // Nếu chuyển sang MAINTENANCE: Kiểm tra phiếu nhập/xuất đang xử lý
            if ($newStatus === 'MAINTENANCE') {
                // Kiểm tra phiếu nhập kho đang xử lý
                // Status: PENDING (chờ xử lý), QC_PASSED (QC đạt chưa confirm), QC_FAILED (QC không đạt chưa xử lý)
                $pendingStockIn = $warehouse->stockInRequests()
                    ->whereIn('status', ['PENDING', 'QC_PASSED', 'QC_FAILED'])
                    ->count();

                // Kiểm tra phiếu chuyển kho đang xử lý (cả kho nguồn và kho đích)
                // Status: PENDING (chờ xử lý), OUT_CONFIRMED (đã xuất kho nguồn), IN_CONFIRMED (đã nhập kho đích)
                $pendingTransfer = $warehouse->transferRequestsFrom()
                    ->whereIn('status', ['PENDING', 'OUT_CONFIRMED', 'IN_CONFIRMED'])
                    ->count()
                    + $warehouse->transferRequestsTo()
                    ->whereIn('status', ['PENDING', 'OUT_CONFIRMED', 'IN_CONFIRMED'])
                    ->count();

                $totalPending = $pendingStockIn + $pendingTransfer;

                if ($totalPending > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Không thể chuyển kho sang trạng thái Bảo trì. Kho đang có {$totalPending} phiếu nhập/xuất/chuyển chưa hoàn thành. Vui lòng hoàn tất các giao dịch trước.");
                }
            }
        }

        $warehouse->update($validated);
        return redirect()->route('admin.inventory.warehouses.index')
                         ->with('success', 'Cập nhật kho hàng thành công.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->loadCount('warehouseStocks');

        if ($warehouse->warehouse_stocks_count > 0) {
            return redirect()->back()->with('error', 'Không thể xóa kho hàng đang có tồn kho. Vui lòng chuyển hàng đi trước.');
        }

        $warehouse->delete();
        return redirect()->route('admin.inventory.warehouses.index')
                         ->with('success', 'Xóa kho hàng thành công.');
    }
}
