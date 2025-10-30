<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id','session_id','code',
        'full_name','phone','email','city','address','note',
        'subtotal','shipping_fee','discount','total',
        'payment_method','payment_status','status'
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


