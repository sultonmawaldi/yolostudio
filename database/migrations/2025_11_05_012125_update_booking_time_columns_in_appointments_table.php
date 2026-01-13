<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Appointment;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        // Tambah dua kolom baru
        Schema::table('appointments', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable()->after('booking_date');
            $table->time('booking_end_time')->nullable()->after('booking_start_time');
        });

        // Konversi data lama dari booking_time (jika masih ada)
        if (Schema::hasColumn('appointments', 'booking_time')) {
            $appointments = Appointment::all();

            foreach ($appointments as $a) {
                if (!empty($a->booking_time) && str_contains($a->booking_time, '-')) {
                    try {
                        [$start, $end] = array_map('trim', explode('-', $a->booking_time));

                        $a->booking_start_time = Carbon::createFromFormat('h:i A', $start)->format('H:i:s');
                        $a->booking_end_time = Carbon::createFromFormat('h:i A', $end)->format('H:i:s');
                        $a->save();
                    } catch (\Exception $e) {
                        \Log::error("Error parsing booking_time for appointment {$a->id}: {$e->getMessage()}");
                    }
                }
            }

            // Hapus kolom lama
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('booking_time');
            });
        }
    }

    public function down()
    {
        // Kembalikan kolom lama
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('booking_time', 255)->nullable();
        });

        // Hapus kolom baru
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });
    }
};

