<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'brand_id',
        'category_id',
        'default_image',
        'base_price',
        'cost_price',
        'total_stock',
        'weight',
        'is_active',
        'visibility',
        'additional',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional' => 'array',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
