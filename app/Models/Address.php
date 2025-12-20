<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'city',
        'district',
        'ward',
        'address',
        'is_default',
        'address_type', // home, office, other
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy địa chỉ đầy đủ (2-level: Tỉnh - Xã)
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,      // Địa chỉ chi tiết
            $this->district,     // Phường/Xã
            $this->city          // Tỉnh/Thành phố
        ]);
        return implode(', ', $parts);
    }
}

