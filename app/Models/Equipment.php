<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_code', 'name', 'category_id', 'brand', 'model', 'year', 'serial_number',
        'operating_weight', 'engine_power', 'bucket_capacity', 'fuel_capacity', 'operating_hours',
        'current_location', 'city', 'province', 'region', 'condition', 'status',
        'daily_rate', 'weekly_rate', 'monthly_rate', 'deposit', 'purchase_price', 'purchase_date',
        'next_service_hours', 'hourly_rate', 'description',
    ];

    protected function casts(): array
    {
        return [
            'operating_weight' => 'decimal:2',
            'engine_power' => 'decimal:2',
            'bucket_capacity' => 'decimal:2',
            'fuel_capacity' => 'decimal:2',
            'operating_hours' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'weekly_rate' => 'decimal:2',
            'monthly_rate' => 'decimal:2',
            'deposit' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
            'next_service_hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(EquipmentImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function utilization()
    {
        return $this->hasMany(EquipmentUtilization::class);
    }

    public function contractItems()
    {
        return $this->hasMany(ContractItem::class);
    }

    public function rentalRequestItems()
    {
        return $this->hasMany(RentalRequestItem::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function assignedOperators()
    {
        return $this->hasMany(Operator::class, 'assigned_equipment_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('equipment_code', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%");
            }))
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['brand'] ?? null, fn ($q, $v) => $q->where('brand', $v))
            ->when($filters['capacity'] ?? null, fn ($q, $v) => $q->where('operating_weight', '>=', $v))
            ->when($filters['location'] ?? null, fn ($q, $v) => $q->where('current_location', 'like', "%{$v}%"))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['sort'] ?? null, fn ($q, $v) => match ($v) {
                'price_asc' => $q->orderBy('daily_rate', 'asc'),
                'price_desc' => $q->orderBy('daily_rate', 'desc'),
                'newest' => $q->orderBy('year', 'desc'),
                'hours_asc' => $q->orderBy('operating_hours', 'asc'),
                default => $q->orderBy('equipment_code', 'asc'),
            }, fn ($q) => $q->orderBy('equipment_code', 'asc'));
    }

    public function utilizationRate($from = null, $to = null)
    {
        $query = $this->utilization();
        if ($from) {
            $query->where('date', '>=', $from);
        }
        if ($to) {
            $query->where('date', '<=', $to);
        }
        $total = (clone $query)->count();
        if ($total === 0) {
            return 0;
        }
        $rented = (clone $query)->where('status', 'rented')->count();

        return round(($rented / $total) * 100, 1);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'available' => 'Available',
            'rented' => 'Rented',
            'maintenance' => 'Maintenance',
            'unavailable' => 'Unavailable',
            default => ucfirst($this->status),
        };
    }
}