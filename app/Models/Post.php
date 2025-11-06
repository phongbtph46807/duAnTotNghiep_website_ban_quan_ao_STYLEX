<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title','slug', 'content', 'author_id'];
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
