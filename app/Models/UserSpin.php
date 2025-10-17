<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSpin extends Model
{
    protected $table = 'user_spins';
    protected $fillable = ['user_id', 'prize_id', 'spin_time', 'is_claimed'];
    public function prize()
    {
        return $this->belongsTo(SpinPrize::class, 'prize_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
