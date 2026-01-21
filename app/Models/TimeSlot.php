<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'slot_group_id',
        'date',
        'start_time',
        'end_time',
        'is_booked',
    ];

    public function slotGroup()
    {
        return $this->belongsTo(SlotGroup::class);
    }

    // Optional (kalau mau)
    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }
}
