<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAddon extends Model
{
    protected $table = 'appointment_addons';

    protected $fillable = [
        'appointment_id',
        'addon_id',
        'price',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'appointment_id' => 'integer',
        'addon_id'       => 'integer',
        'price'          => 'integer',
        'qty'            => 'integer',
        'subtotal'       => 'integer',
    ];

    /* =======================
     | RELATIONS
     =======================*/

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /* =======================
     | MODEL EVENTS
     =======================*/

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->subtotal = $model->price * $model->qty;
        });
    }

    /* =======================
     | ACCESSORS
     =======================*/

    public function getSubtotalFormattedAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.');
    }
}
