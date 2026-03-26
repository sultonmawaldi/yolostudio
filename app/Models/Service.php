<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use SoftDeletes;

    /**
     * Kolom yang boleh di-mass assign
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'image',
        'excerpt',
        'body',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'price',
        'sale_price',
        'reward_points',
        'min_people',
        'max_people',
        'extra_price_per_person',
        'dp_amount',
        'video',
        'featured',
        'status',
        'other',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'extra_price_per_person' => 'float',
        'reward_points' => 'integer',
        'min_people' => 'integer',
        'max_people' => 'integer',
        'dp_amount' => 'integer',
        'featured' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Auto-generate slug dari title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('title')) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    /**
     * ================== RELATIONS ==================
     */

    // Kategori layanan
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Booking / appointment
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Employee yang handle service
    public function employees()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('duration', 'break_duration')
            ->withTimestamps();
    }

    // Addon aktif untuk service ini
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_service')
            ->withTimestamps()
            ->where('addons.is_active', true)
            ->orderBy('addons.sort_order');
    }


    // Background service (aktif & urut)
    public function backgrounds()
    {
        return $this->hasMany(ServiceBackground::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // Alias jika hanya mau background aktif
    public function activeBackgrounds()
    {
        return $this->backgrounds();
    }

    // Transaksi
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)
            ->withPivot('price', 'qty')
            ->withTimestamps();
    }

    // Pricelist
    public function pricelists()
    {
        return $this->hasMany(Pricelist::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_service');
    }
}
