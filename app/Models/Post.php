<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_PRIVATE = 'private';
    const STATUS_SCHEDULED = 'scheduled';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'content',
        'thumbnail',
        'status',
        'views',
        'is_hot',
        'published_at',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Accessor để lấy URL ảnh thumbnail
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return asset('client/images/blog-default.jpg');
        }

        // Nếu thumbnail bắt đầu bằng 'assets/' thì dùng asset() (public folder)
        if (str_starts_with($this->thumbnail, 'assets/')) {
            return asset($this->thumbnail);
        }

        // Nếu là đường dẫn storage thì dùng Storage::url()
        if (file_exists(storage_path('app/public/' . $this->thumbnail))) {
            return Storage::url($this->thumbnail);
        }

        // Fallback về asset nếu không tìm thấy
        return asset($this->thumbnail);
    }
}
