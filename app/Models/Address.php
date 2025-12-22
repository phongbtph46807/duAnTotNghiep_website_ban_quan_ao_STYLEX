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
     * Cấu trúc mới: chỉ có Tỉnh/Thành phố và Phường/Xã (không có Quận/Huyện)
     * Format: Phường/Xã, Tỉnh/Thành phố (bỏ địa chỉ chi tiết nếu chỉ là số)
     */
    public function getFullAddressAttribute()
    {
        // Nếu có ward (dữ liệu cũ), dùng ward làm phường/xã
        // Nếu không có ward, dùng district làm phường/xã
        $commune = $this->ward ?: $this->district;
        
        // Làm sạch địa chỉ chi tiết
        $addressDetail = trim($this->address ?? '');
        
        // Nếu address chỉ chứa số và dấu phẩy/dấu cách (có thể là mã bưu điện hoặc số nhà không rõ ràng)
        // thì chỉ hiển thị Phường/Xã và Tỉnh/Thành phố
        if (preg_match('/^[\d\s,\.]+$/', $addressDetail)) {
            $parts = array_filter([
                $commune,            // Phường/Xã
                $this->city          // Tỉnh/Thành phố
            ]);
        } else {
            // Nếu có địa chỉ chi tiết hợp lệ, hiển thị đầy đủ
            $parts = array_filter([
                $addressDetail,      // Địa chỉ chi tiết
                $commune,            // Phường/Xã
                $this->city          // Tỉnh/Thành phố
            ]);
        }
        
        return implode(', ', $parts);
    }
    
    /**
     * Lấy tên phường/xã (commune) - ưu tiên ward, nếu không có thì dùng district
     */
    public function getCommuneAttribute()
    {
        return $this->ward ?: $this->district;
    }
}

