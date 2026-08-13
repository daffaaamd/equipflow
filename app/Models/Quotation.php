<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number', 'rental_request_id', 'customer_id', 'project_id', 'valid_until',
        'rental_period_start', 'rental_period_end', 'rental_rate', 'operator_cost',
        'transportation_cost', 'fuel_cost', 'additional_service_cost', 'discount',
        'tax_rate', 'subtotal', 'tax_amount', 'grand_total', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'rental_period_start' => 'date',
            'rental_period_end' => 'date',
            'rental_rate' => 'decimal:2',
            'operator_cost' => 'decimal:2',
            'transportation_cost' => 'decimal:2',
            'fuel_cost' => 'decimal:2',
            'additional_service_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function getDurationDaysAttribute()
    {
        if ($this->rental_period_start && $this->rental_period_end) {
            return $this->rental_period_start->diffInDays($this->rental_period_end) + 1;
        }

        return 30;
    }
}