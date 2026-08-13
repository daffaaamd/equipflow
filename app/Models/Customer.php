<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code', 'user_id', 'company_name', 'contact_person', 'email', 'phone',
        'address', 'city', 'province', 'region', 'industry', 'tax_id', 'segment', 'status', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function getTotalRentalValueAttribute()
    {
        return $this->contracts()->whereIn('status', ['active', 'completed'])->sum('contract_value');
    }

    public function getActiveContractsAttribute()
    {
        return $this->contracts()->where('status', 'active')->count();
    }

    public function getOutstandingAttribute()
    {
        return $this->invoices()->whereIn('payment_status', ['pending', 'partial', 'overdue'])
            ->get()->sum(fn ($i) => $i->total - $i->amount_paid);
    }
}