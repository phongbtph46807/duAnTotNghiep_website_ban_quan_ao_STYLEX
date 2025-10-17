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
        'quantity',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'quantity' => 'integer',
        'status' => 'integer',
    ];
}
