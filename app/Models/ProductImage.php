<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_id',
        'image_url',
        'sort_order',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    // Liên kết với sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Liên kết với biến thể sản phẩm (nếu có)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
