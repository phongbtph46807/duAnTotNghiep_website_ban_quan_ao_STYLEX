<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'texture_id',
        'sku',
        'image',
        'price',
        'cost_price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'status' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function texture()
    {
        return $this->belongsTo(Texture::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'variant_id');
    }

    public function getTotalAvailableStock(): int
    {
        // Chỉ tính tồn kho từ các kho vật lý đang hoạt động
        return (int) $this->warehouseStocks()
            ->whereHas('warehouse', function($query) {
                $query->where('operational_status', 'ACTIVE')
                      ->where('type', 'PHYSICAL');
            })
            ->sum('available');
    }
}
