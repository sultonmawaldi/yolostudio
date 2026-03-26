<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'code',
        'name',
        'price',
        'unit',        // person, minute, item
        'max_qty',
        'is_active',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'price'     => 'integer',
        'max_qty'   => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Addon digunakan pada banyak appointment
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_addons')
            ->withPivot(['price', 'qty', 'subtotal'])
            ->withTimestamps();
    }

    // Addon tersedia untuk banyak service
    public function services()
    {
        return $this->belongsToMany(Service::class, 'addon_service');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    public function calculateSubtotal(int $qty): int
    {
        return $this->price * $qty;
    }

    public function isUnlimited(): bool
    {
        return is_null($this->max_qty);
    }
}
