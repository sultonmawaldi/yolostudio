<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;

class FillBookingTimeSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = Appointment::whereNull('booking_time')
            ->orWhere('booking_time', '')
            ->get();

        foreach ($appointments as $appointment) {
            // Default waktu acak per 15 menit
            $startTimes = ['09:00 AM', '09:15 AM', '09:30 AM', '10:00 AM', '10:15 AM', '10:30 AM', '11:00 AM'];
            $randomStart = $startTimes[array_rand($startTimes)];
            
            // Tambahkan 15 menit
            $startTimestamp = strtotime($randomStart);
            $endTimestamp = strtotime('+15 minutes', $startTimestamp);
            $endTime = date('h:i A', $endTimestamp);

            $appointment->booking_time = "$randomStart - $endTime";
            $appointment->save();
        }

        $count = count($appointments);
        echo "✅ Filled booking_time for $count appointments.\n";
    }
}
