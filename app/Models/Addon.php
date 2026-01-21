<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    // ======================
    // RELATIONS
    // ======================

    /**
     * Addon digunakan pada banyak appointment
     */
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_addons')
            ->withPivot(['price', 'qty', 'subtotal'])
            ->withTimestamps();
    }

    /**
     * Addon tersedia untuk banyak service
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'addon_service');
    }

    // ======================
    // SCOPES
    // ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ======================
    // ACCESSORS
    // ======================

    public function getPriceFormattedAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
