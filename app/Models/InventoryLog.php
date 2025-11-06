<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'change',
        'reason',
        'reference_id',
    ];

    /**
     * Mối quan hệ với ProductVariant
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Tự động cập nhật stock của variant khi ghi log
     */
    protected static function booted()
    {
        static::created(function ($log) {
            $variant = $log->variant;
            if ($variant) {
                $variant->increment('stock_quantity', $log->change);
            }
        });
    }
}
