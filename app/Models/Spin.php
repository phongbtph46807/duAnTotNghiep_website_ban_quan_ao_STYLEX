<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'voucher_id',
        'is_active',
        'probability'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function spinUsers()
    {
        return $this->hasMany(SpinUser::class);
    }
}
