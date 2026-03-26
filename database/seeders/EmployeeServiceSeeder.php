<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeServiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('employee_service')->insert([
            [
                'employee_id' => 4,
                'service_id' => 1,
                'slot_group_id' => 1,
                'duration' => 15,
                'break_duration' => 15,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 2,
                'slot_group_id' => 1,
                'duration' => 15,
                'break_duration' => 15,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 3,
                'slot_group_id' => 1,
                'duration' => 10,
                'break_duration' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 4,
                'slot_group_id' => 2,
                'duration' => 5,
                'break_duration' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 5,
                'slot_group_id' => 3,
                'duration' => 5,
                'break_duration' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 6,
                'slot_group_id' => 4,
                'duration' => 5,
                'break_duration' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 4,
                'service_id' => 7,
                'slot_group_id' => 5,
                'duration' => 5,
                'break_duration' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
