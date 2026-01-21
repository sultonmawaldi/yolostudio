<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeService extends Pivot
{
    protected $table = 'employee_service';

    protected $fillable = [
        'employee_id',
        'service_id',
        'slot_group_id',
        'duration',
        'break_duration',
    ];

    public function slotGroup()
    {
        return $this->belongsTo(SlotGroup::class, 'slot_group_id');
    }
}
