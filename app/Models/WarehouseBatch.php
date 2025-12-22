<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseBatch extends Model
{
    protected $fillable = [
        'warehouse_id',
        'variant_id',
        'batch_number',
        'location',
        'quantity',
        'cost_price',
        'received_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price' => 'integer',
        'received_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
