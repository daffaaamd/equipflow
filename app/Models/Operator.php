<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_code', 'name', 'email', 'phone', 'certification', 'certification_expiry',
        'license_number', 'years_experience', 'assigned_equipment_id', 'project_id',
        'working_hours', 'availability', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'certification_expiry' => 'date',
            'working_hours' => 'decimal:2',
        ];
    }

    public function assignedEquipment()
    {
        return $this->belongsTo(Equipment::class, 'assigned_equipment_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function isCertificationExpiring(): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->lte(now()->addDays(60));
    }

    public function isCertificationExpired(): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->lt(now());
    }
}