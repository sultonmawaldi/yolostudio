<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    // use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'days' => 'array',
        'social' => 'array',
    ];

    /**
     * Hari libur per employee
     */
    public function holidays()
    {
        return $this->hasMany(Holiday::class, 'employee_id');
    }

    /**
     * Relasi ke service dengan pivot data
     * - duration: durasi sesi per service
     * - break_duration: waktu persiapan per service
     * - slot_duration: opsional (kalau nanti mau per-service juga)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->using(EmployeeService::class)
            ->withPivot('duration', 'break_duration', 'slot_group_id')
            ->withTimestamps();
    }




    /**
     * Relasi ke user yang memiliki employee ini
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke appointment (booking)
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // App\Models\Employee.php
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
