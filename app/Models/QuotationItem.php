<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = ['quotation_id', 'equipment_id', 'equipment_name_snapshot', 'quantity', 'unit', 'unit_rate', 'duration_days', 'line_total'];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}