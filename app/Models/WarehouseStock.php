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
        'clearance',
    ];

    protected $casts = [
        'on_hand' => 'integer',
        'available' => 'integer',
        'reserved' => 'integer',
        'quarantine' => 'integer',
        'damaged' => 'integer',
        'clearance' => 'integer',
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
        return $this->available + $this->reserved + $this->quarantine + $this->damaged + ($this->clearance ?? 0);
    }

    public function getSellableStock(): int
    {
        return $this->available;
    }

    public function syncOnHand(): void
    {
        $total = $this->available + $this->reserved + $this->quarantine + $this->damaged + ($this->clearance ?? 0);
        if ($this->on_hand !== $total) {
            $this->update(['on_hand' => $total]);
        }
    }
}
