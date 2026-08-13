<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentImage extends Model
{
    use HasFactory;

    protected $fillable = ['equipment_id', 'image_url', 'caption', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function getUrlAttribute()
    {
        $url = $this->image_url ?? 'img/placeholder.svg';
        if (\Illuminate\Support\Str::startsWith($url, 'http')) {
            return $url;
        }
        return '/' . ltrim($url, '/');
    }
}