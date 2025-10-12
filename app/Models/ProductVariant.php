<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'color_id',
        'size_id',
        'price',
        'stock_quantity',
        'is_default',
        'image',
        'attributes',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'attributes' => 'array',
    ];

    // Quan hệ ngược về Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ với bảng màu
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    // Quan hệ với bảng size
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    // Quan hệ với bảng texture
    public function texture()
    {
        return $this->belongsTo(Texture::class);
    }
}
    
