<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Studio extends Model
{
    protected $table = 'studios';

    protected $fillable = [
        'name',
        'slug',
        'city',
        'address',
        'phone',
        'calendar_id',
        'google_maps',
        'image',
        'status',
    ];

    /**
     * Auto generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($studio) {
            if (empty($studio->slug)) {
                $studio->slug = Str::slug($studio->name);
            }
        });
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // App\Models\Studio.php
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
