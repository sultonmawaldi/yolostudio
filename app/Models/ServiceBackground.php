<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBackground extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'type',        // color | image
        'value',       // #ffffff | url
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
