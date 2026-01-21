<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotGroup extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'slot_duration',
        'break_duration',
        'start_time',       // jam mulai default
        'end_time',         // jam selesai default
        'working_hours',    // array jam kerja, termasuk jam istirahat
    ];

    protected $casts = [
        'working_hours' => 'array', // otomatis JSON -> array
    ];

    public function employeeServices()
    {
        return $this->hasMany(EmployeeService::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
