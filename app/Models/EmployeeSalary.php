<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    protected $table = 'employee_salaries';

    protected $fillable = [
        'user_id',
        'base_salary',
        'bonus',
        'deduction',
        'month',
        'year',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'integer',
        'bonus' => 'integer',
        'deduction' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalSalary(): int
    {
        return $this->base_salary + $this->bonus - $this->deduction;
    }
}
