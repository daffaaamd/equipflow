<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image_url', 'sort_order'];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    public function getAvailableCountAttribute()
    {
        return $this->equipment()->where('status', 'available')->count();
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