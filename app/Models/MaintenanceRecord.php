<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_number', 'equipment_id', 'type', 'title', 'description', 'technician',
        'date', 'cost', 'downtime_hours', 'parts_used', 'next_due_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'next_due_date' => 'date',
            'cost' => 'decimal:2',
            'downtime_hours' => 'decimal:2',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')->whereDate('date', '>=', now());
    }
}