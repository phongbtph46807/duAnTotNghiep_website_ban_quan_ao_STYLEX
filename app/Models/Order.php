<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id','session_id','code',
        'buyer_name','buyer_phone','buyer_email',
        'full_name','phone','email','city','address','note',
        'subtotal','shipping_fee','discount',
        'tax_rate_id','tax_amount',
        'shipping_carrier_id',
        'total','total_cost',
        'payment_method','payment_status','status',
        'momo_order_id','momo_trans_id',
        'cancel_reason','cancel_images',
        'return_reason','return_images',
        'updated_by'
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'discount' => 'integer',
        'tax_amount' => 'integer',
        'total' => 'integer',
        'cancel_images' => 'array',
        'return_images' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_rate_id');
    }

    public function shippingCarrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'shipping_carrier_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function picking()
    {
        return $this->hasOne(OrderPicking::class, 'order_id', 'id');
    }
}
