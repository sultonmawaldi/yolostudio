<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = now()->startOfDay();
        $daysAhead = 14; // generate 2 minggu ke depan

        // Ambil semua employee_service yang sudah punya slot group
        $employeeServices = DB::table('employee_service')
            ->whereNotNull('slot_group_id')
            ->whereNotNull('duration')
            ->get();

        foreach ($employeeServices as $es) {

            $slotGroup = DB::table('slot_groups')
                ->where('id', $es->slot_group_id)
                ->first();

            if (!$slotGroup || !$slotGroup->start_time || !$slotGroup->end_time) {
                continue;
            }

            $duration = (int) $es->duration;
            $break    = (int) ($slotGroup->break_duration ?? 0);

            for ($i = 0; $i < $daysAhead; $i++) {

                $date = $startDate->copy()->addDays($i);

                // Optional: skip hari Minggu
                // if ($date->isSunday()) continue;

                $current = Carbon::parse($date->toDateString() . ' ' . $slotGroup->start_time);
                $end     = Carbon::parse($date->toDateString() . ' ' . $slotGroup->end_time);

                while ($current->copy()->addMinutes($duration)->lte($end)) {

                    $slotEnd = $current->copy()->addMinutes($duration);

                    TimeSlot::firstOrCreate(
                        [
                            'slot_group_id' => $slotGroup->id,
                            'date'          => $date->toDateString(),
                            'start_time'    => $current->format('H:i:s'),
                            'end_time'      => $slotEnd->format('H:i:s'),
                        ],
                        [
                            'is_booked' => false,
                        ]
                    );

                    $current->addMinutes($duration + $break);
                }
            }
        }

        $this->command->info('✅ Time slots generated successfully.');
    }
}
