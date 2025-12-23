<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectAssessment extends Model
{
    protected $fillable = [
        'warehouse_id', 'variant_id', 'batch_number', 'quantity', 'defect_level', 'defect_type', 'defect_description', 'description',
        'classification', 'repair_cost', 'material_cost', 'status', 'location',
        'created_by', 'assessed_by', 'approved_by', 'completed_by', 'rejected_by',
        'rejection_reason', 'notes', 'stock_in_request_id'
    ];

    protected $casts = [
        'repair_cost' => 'decimal:0',
        'material_cost' => 'decimal:0',
    ];

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

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function stockInRequest()
    {
        return $this->belongsTo(StockInRequest::class);
    }
}
