<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number', 'customer_id', 'project_id', 'contact_person', 'contact_phone',
        'project_name', 'project_type', 'project_location', 'operator_required',
        'transportation_included', 'fuel_included', 'additional_requirements',
        'status', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'operator_required' => 'boolean',
            'transportation_included' => 'boolean',
            'fuel_included' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
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
        return $this->hasMany(RentalRequestItem::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getEarliestStartAttribute()
    {
        $val = $this->items->min('start_date');
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function getLatestEndAttribute()
    {
        $val = $this->items->max('end_date');
        return $val ? \Carbon\Carbon::parse($val) : null;
    }
}