<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'service_id',      // nullable → bisa global
        'title',
        'description',
        'price',
        'features',        // array/json
        'category',        // optional grouping (Basic, Premium, dll)
        'button_text',     // CTA button
        'button_link',
        'is_active',
        'sort_order',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'features'  => 'array',
        'is_active' => 'boolean',
        'price'     => 'integer',
    ];

    /**
     * ======================
     * Relationships
     * ======================
     */

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * ======================
     * Query Scopes
     * ======================
     */

    // Hanya pricelist aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Urutkan default
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Filter kategori (misal: Basic / Premium)
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Pricelist global (tidak terikat service)
    public function scopeGlobal($query)
    {
        return $query->whereNull('service_id');
    }

    // Pricelist khusus service
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * ======================
     * Accessors
     * ======================
     */

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getHasButtonAttribute(): bool
    {
        return !empty($this->button_text) && !empty($this->button_link);
    }
}
