<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code', 'name', 'customer_id', 'industry', 'location', 'city', 'province',
        'region', 'start_date', 'end_date', 'contract_value', 'status', 'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'contract_value' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function utilization()
    {
        return $this->hasMany(EquipmentUtilization::class);
    }

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    public function getEquipmentCountAttribute()
    {
        return $this->contracts()
            ->whereIn('status', ['draft', 'active'])
            ->with('items')->get()
            ->sum(fn ($c) => $c->items->sum('quantity'));
    }
}