<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'description',
        'min_people',
        'max_people',
        'extra_price_per_person',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
        'min_people' => 'integer',
        'max_people' => 'integer',
        'extra_price_per_person' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Kategori layanan
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Booking / appointment
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Employee yang bisa handle service
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('duration', 'break_duration')
            ->withTimestamps();
    }

    /**
     * Addon yang tersedia untuk service ini
     */
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_service')
            ->withTimestamps()
            ->where('addons.is_active', true);
    }

    public function backgrounds()
    {
        return $this->hasMany(ServiceBackground::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function activeBackgrounds()
    {
        return $this->hasMany(ServiceBackground::class)->where('is_active', true);
    }
}
