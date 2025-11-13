<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReport extends Model
{
    //
    protected $table = 'admin_reports';

    protected $fillable = [
        'user_id',
        'report_date',
        'total_salary_paid',
        'total_commission',
        'orders_processed_count',
        'inventory_transactions_count',
    ];
}
