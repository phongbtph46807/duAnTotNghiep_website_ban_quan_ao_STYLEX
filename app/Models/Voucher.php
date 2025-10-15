<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Voucher extends Model
{
    // Assuming the Voucher model has at least an 'id' and 'code' attribute
    protected $table = 'vouchers';
    protected $fillable = ['code', 'type', 'value', 'min_order_value','max_discount_amount','usage_limit','expires_at'];
}
