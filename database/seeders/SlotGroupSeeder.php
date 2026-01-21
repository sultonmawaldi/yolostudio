<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SlotGroup;

class SlotGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Employee utama / studio
        $employeeId = 1;

        // SLOT GROUP: Self Photo + Pas Foto
        SlotGroup::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'name'        => 'Studio Session',
            ],
            [
                'start_time'     => '09:00',
                'end_time'       => '17:00',
                'slot_duration'  => 30,
                'break_duration' => 5,
            ]
        );

        // SLOT GROUP: Photobox
        SlotGroup::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'name'        => 'Photobox',
            ],
            [
                'start_time'     => '10:00',
                'end_time'       => '22:00',
                'slot_duration'  => 5,
                'break_duration' => 0,
            ]
        );
    }
}
