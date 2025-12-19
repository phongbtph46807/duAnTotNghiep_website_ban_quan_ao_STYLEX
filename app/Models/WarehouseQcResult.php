<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseQcResult extends Model
{
    protected $fillable = [
        'request_type', 'request_id', 'variant_id', 'total_qty',
        'passed_qty', 'failed_qty', 'qc_by', 'qc_at', 'qc_notes'
    ];

    protected $casts = [
        'qc_at' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function qcBy()
    {
        return $this->belongsTo(User::class, 'qc_by');
    }

    public function stockInRequest()
    {
        return $this->belongsTo(StockInRequest::class, 'request_id')
                    ->where('request_type', 'STOCK_IN');
    }

    public function stockOutRequest()
    {
        return $this->belongsTo(StockOutRequest::class, 'request_id')
                    ->where('request_type', 'STOCK_OUT');
    }

    public function getPassRateAttribute()
    {
        return $this->total_qty > 0 ? round(($this->passed_qty / $this->total_qty) * 100, 2) : 0;
    }

    public function getFailRateAttribute()
    {
        return $this->total_qty > 0 ? round(($this->failed_qty / $this->total_qty) * 100, 2) : 0;
    }
}