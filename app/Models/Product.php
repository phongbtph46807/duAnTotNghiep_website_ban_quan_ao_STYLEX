<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'thumbnail',
        'is_active',
        'description',
        'is_featured',
        'meta_title',
        'price',
        'price_sale',
    ];

    protected $casts = [
        'is_active'   => 'integer',
        'is_featured' => 'integer',
        'price'       => 'decimal:0',
        'price_sale'  => 'decimal:0',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // Accessor để lấy URL ảnh thumbnail
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail && file_exists(storage_path('app/public/' . $this->thumbnail))) {
            return \Storage::url($this->thumbnail);
        }
        return asset('client/images/banner-01.jpg'); // Ảnh mặc định
    }

    // Accessor để lấy URL ảnh chính
    public function getDefaultImageUrlAttribute()
    {
        if ($this->primaryImage) {
            return \Storage::url($this->primaryImage->image_path);
        }
        if ($this->thumbnail) {
            return \Storage::url($this->thumbnail);
        }
        return asset('client/images/no-image.jpg'); // Ảnh mặc định
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function getRatingsCountAttribute()
    {
        $reviews = $this->reviews;
        return [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
    }
    public function getRatingsPercentAttribute()
    {
        $total = $this->reviews->count();
        if ($total === 0) {
            return [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        }

        $percentages = [];
        foreach ($this->ratings_count as $star => $count) {
            $percentages[$star] = round(($count / $total) * 100, 1);
        }

        return $percentages;
    }

    public function getExperienceAvgAttribute()
    {
        $reviews = $this->reviews()->with('experiences')->get();
        $experiences = $reviews->pluck('experiences')->flatten();

        $criteria = [
            'Chất liệu vải',
            'Độ vừa vặn',
            'Màu sắc',
        ];

        $data = [];
        foreach ($criteria as $criterion) {
            $filtered = $experiences->where('criterion', $criterion);
            $data[$criterion] = [
                'avg' => round($filtered->avg('rating') ?? 0, 1),
                'count' => $filtered->count(),
            ];
        }

        return $data;
    }
}
