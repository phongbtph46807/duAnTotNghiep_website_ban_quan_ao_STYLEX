<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinPrize extends Model
{
    protected $table = 'spin_prizes';
    protected $fillable = ['name', 'probability', 'value_reference', 'type'];
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'value_reference', 'id');
    }
}
