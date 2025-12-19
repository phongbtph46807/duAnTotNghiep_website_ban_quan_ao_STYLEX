<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutInvoiceItem extends Model
{
    protected $fillable = [
        'stock_out_invoice_id', 'variant_id', 'quantity', 'unit_price',
        'line_total', 'defect_assessment_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'line_total' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(StockOutInvoice::class, 'stock_out_invoice_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function defectAssessment(): BelongsTo
    {
        return $this->belongsTo(DefectAssessment::class);
    }
}
