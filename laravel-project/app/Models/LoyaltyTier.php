<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class LoyaltyTier extends Model
{
    protected $fillable = ['name', 'min_spend_required', 'discount_rate'];

    // Quan hệ với User (một hạng thành viên có nhiều người dùng)
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
