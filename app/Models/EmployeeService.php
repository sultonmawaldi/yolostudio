<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeService extends Pivot
{
    protected $table = 'employee_service';

    protected $fillable = [
        'employee_id',
        'service_id',
        'duration',
        'break_duration',
        'slot_duration', // optional jika dipakai
    ];
}

