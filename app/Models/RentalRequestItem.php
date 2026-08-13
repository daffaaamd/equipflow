<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalRequestItem extends Model
{
    use HasFactory;

    protected $fillable = ['rental_request_id', 'equipment_id', 'equipment_category_id', 'quantity', 'start_date', 'end_date', 'notes'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }
}