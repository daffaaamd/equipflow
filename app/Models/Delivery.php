<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_number', 'contract_id', 'equipment_id', 'customer_id', 'project_id',
        'pickup_location', 'destination', 'driver_name', 'driver_phone', 'transport_vehicle',
        'plate_number', 'delivery_date', 'estimated_arrival', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'estimated_arrival' => 'date',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}