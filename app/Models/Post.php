<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title','slug', 'content', 'author_id'];
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
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
