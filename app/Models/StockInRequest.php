<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInRequest extends Model
{
    protected $fillable = [
        'warehouse_id', 'variant_id', 'batch_number', 'quantity', 'cost_price',
        'received_date', 'supplier_name', 'supplier_contact', 'invoice_number',
        'status', 'confirmed_at',
        'created_by', 'confirmed_by', 'notes'
    ];

    protected $casts = [
        'received_date' => 'date',
        'confirmed_at' => 'datetime',
        'cost_price' => 'decimal:2',
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_QC_PASSED = 'QC_PASSED';
    const STATUS_QC_FAILED = 'QC_FAILED';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_QC_PASSED => 'QC đạt',
            self::STATUS_QC_FAILED => 'QC không đạt',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function canBeQc()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeConfirmed()
    {
        return $this->status === self::STATUS_QC_PASSED;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_QC_FAILED]);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function latestQcResult()
    {
        return $this->hasOne(WarehouseQcResult::class, 'request_id')
                    ->where('request_type', 'STOCK_IN')
                    ->latest();
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function approvals()
    {
        return $this->morphMany(InventoryApproval::class, 'approvable');
    }

    public function returnRequests()
    {
        return $this->hasMany(StockReturnRequest::class);
    }

    public function qcResults()
    {
        return $this->hasMany(WarehouseQcResult::class, 'request_id')
                    ->where('request_type', 'STOCK_IN');
    }

    public function notes()
    {
        return $this->hasMany(WarehouseNote::class, 'record_id')
                    ->where('table_name', 'stock_in_requests');
    }
}
