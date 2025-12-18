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
        'status',
        'notes',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getTotalSalary(): float
    {
        return $this->base_salary + $this->bonus - $this->deduction;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'pending';
    }

    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeByRole($query, $role)
    {
        return $query->whereHas('user', function($q) use ($role) {
            $q->where('role', $role);
        });
    }
}