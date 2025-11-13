<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpinUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'spin_id',
        'time_spin',
        'is_claimed'
    ];

    protected $casts = [
        'time_spin' => 'datetime',
        'is_claimed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spin()
    {
        return $this->belongsTo(Spin::class);
    }
}
