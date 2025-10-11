<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'thumbnail',
        'status',
        'description',
        'is_featured',
        'meta_title',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
