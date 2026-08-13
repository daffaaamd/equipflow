<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractItem extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'equipment_id', 'quantity', 'unit_rate', 'duration_days', 'line_total'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}