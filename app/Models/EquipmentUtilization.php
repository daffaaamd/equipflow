<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentUtilization extends Model
{
    use HasFactory;

    protected $table = 'equipment_utilization';

    protected $fillable = ['equipment_id', 'date', 'status', 'hours_operated', 'revenue', 'project_id', 'contract_id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'hours_operated' => 'decimal:2',
            'revenue' => 'decimal:2',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}