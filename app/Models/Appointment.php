<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    // ======================
    // RELASI UTAMA
    // ======================

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function background()
    {
        return $this->belongsTo(ServiceBackground::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // ======================
    // ADDONS (BEST PRACTICE)
    // ======================

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'appointment_addons')
            ->withPivot(['price', 'qty', 'subtotal'])
            ->withTimestamps();
    }
}
