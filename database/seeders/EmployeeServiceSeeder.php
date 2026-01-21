<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeService;
use App\Models\SlotGroup;
use App\Models\Service;

class EmployeeServiceSeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = 1;

        // Ambil slot groups
        $studioSlot = SlotGroup::where('employee_id', $employeeId)
            ->where('name', 'Studio Session')
            ->firstOrFail();

        $photoboxSlot = SlotGroup::where('employee_id', $employeeId)
            ->where('name', 'Photobox')
            ->firstOrFail();

        // === SERVICE STUDIO (Self Photo & Pas Foto) ===
        $studioServices = Service::whereIn('slug', [
            'personal-self-photo-studio',
            'pas-photo',
            'pas-foto-background-merah',
            'pas-foto-background-biru',
            'pas-foto-background-hijau',
        ])->get();

        foreach ($studioServices as $service) {
            EmployeeService::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'service_id'  => $service->id,
                ],
                [
                    'slot_group_id' => $studioSlot->id,
                    'duration' => 30,
                    'break_duration' => 5,
                ]
            );
        }

        // === SERVICE PHOTOBOX ===
        $photoboxServices = Service::whereIn('slug', [
            'wide-box',
            'corner-box',
            'snap-up-box',
        ])->get();

        foreach ($photoboxServices as $service) {
            EmployeeService::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'service_id'  => $service->id,
                ],
                [
                    'slot_group_id' => $photoboxSlot->id,
                    'duration' => 5,
                    'break_duration' => 0,
                ]
            );
        }
    }
}
