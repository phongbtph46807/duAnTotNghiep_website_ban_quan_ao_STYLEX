<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseNote extends Model
{
    protected $fillable = [
        'table_name', 'record_id', 'content', 'created_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockInRequest()
    {
        return $this->belongsTo(StockInRequest::class, 'record_id')
                    ->where('table_name', 'stock_in_requests');
    }

    public function stockOutRequest()
    {
        return $this->belongsTo(StockOutRequest::class, 'record_id')
                    ->where('table_name', 'stock_out_requests');
    }

    public function transferRequest()
    {
        return $this->belongsTo(TransferRequest::class, 'record_id')
                    ->where('table_name', 'transfer_requests');
    }
}