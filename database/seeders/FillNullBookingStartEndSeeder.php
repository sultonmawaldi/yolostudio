<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FillNullBookingStartEndSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua appointments yang booking_start_time atau booking_end_time null
        $appointments = DB::table('appointments')
            ->whereNull('booking_start_time')
            ->orWhereNull('booking_end_time')
            ->get();

        foreach ($appointments as $a) {
            // Atur default start time (misal 09:00 AM) atau random di jam kerja
            $start = Carbon::createFromTime(rand(9, 16), 0, 0); // jam 9 - 16
            $end = $start->copy()->addHour(); // durasi 1 jam

            DB::table('appointments')->where('id', $a->id)->update([
                'booking_start_time' => $start->format('H:i:s'),
                'booking_end_time' => $end->format('H:i:s'),
            ]);
        }

        $this->command->info('Semua kolom booking_start_time & booking_end_time yang NULL telah diisi.');
    }
}
