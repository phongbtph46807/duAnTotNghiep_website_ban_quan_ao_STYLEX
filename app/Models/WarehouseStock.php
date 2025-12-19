<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseStock extends Model
{
    protected $table = 'warehouse_stocks';

    protected $fillable = [
        'warehouse_id',
        'variant_id',
        'on_hand',
        'available',
        'reserved',
        'quarantine',
        'damaged',
    ];

    protected $casts = [
        'on_hand' => 'integer',
        'available' => 'integer',
        'reserved' => 'integer',
        'quarantine' => 'integer',
        'damaged' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getTotalStock(): int
    {
        return $this->on_hand;
    }

    public function getSellableStock(): int
    {
        return $this->available;
    }
}
