<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'operational_status',
        'address',
    ];

    protected $casts = [
        'operational_status' => 'string',
    ];

    /**
     * Quan hệ: Tồn kho chi tiết tại kho này.
     * Một kho có nhiều bản ghi tồn kho chi tiết (mỗi SP 1 bản ghi).
     */
    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_id');
    }

    /**
     * Alias cho warehouseStocks()
     */
    public function stocks(): HasMany
    {
        return $this->warehouseStocks();
    }

    /**
     * Quan hệ: Các log (sổ cái) liên quan đến kho này.
     */
    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'warehouse_id');
    }

    /**
     * Quan hệ: Phiếu nhập kho
     */
    public function stockInRequests(): HasMany
    {
        return $this->hasMany(StockInRequest::class, 'warehouse_id');
    }

    /**
     * Quan hệ: Phiếu chuyển kho (kho nguồn)
     */
    public function transferRequestsFrom(): HasMany
    {
        return $this->hasMany(TransferRequest::class, 'from_warehouse_id');
    }

    /**
     * Quan hệ: Phiếu chuyển kho (kho đích)
     */
    public function transferRequestsTo(): HasMany
    {
        return $this->hasMany(TransferRequest::class, 'to_warehouse_id');
    }

    /**
     * Quan hệ: Phiếu kiểm kê
     */
    public function countRequests(): HasMany
    {
        return $this->hasMany(CountRequest::class, 'warehouse_id');
    }

    /**
     * Quan hệ: Ghi chú kho
     */
    public function notes(): HasMany
    {
        return $this->hasMany(WarehouseNote::class, 'warehouse_id');
    }
}
