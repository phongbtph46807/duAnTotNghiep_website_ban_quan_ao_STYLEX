<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleSalary extends Model
{
    protected $fillable = ['role_id', 'base_salary'];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}