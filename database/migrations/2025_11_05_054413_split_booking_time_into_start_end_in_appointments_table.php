<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣ Tambah kolom baru
        Schema::table('appointments', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable()->after('booking_time');
            $table->time('booking_end_time')->nullable()->after('booking_start_time');
        });

        // 2️⃣ Migrasi data dari kolom lama
        $appointments = DB::table('appointments')->get();

        foreach ($appointments as $a) {
            if ($a->booking_time) {
                $times = explode(' - ', $a->booking_time);

                if (count($times) === 2) {
                    try {
                        $start = Carbon::createFromFormat('g:i A', trim($times[0]))->format('H:i:s');
                        $end   = Carbon::createFromFormat('g:i A', trim($times[1]))->format('H:i:s');

                        DB::table('appointments')->where('id', $a->id)->update([
                            'booking_start_time' => $start,
                            'booking_end_time' => $end,
                        ]);
                    } catch (\Exception $e) {
                        \Log::warning("Format booking_time salah untuk ID {$a->id}");
                    }
                }
            }
        }

        // 3️⃣ Drop kolom lama setelah data berhasil dimigrasi
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('booking_time');
        });
    }

    public function down()
    {
        // Kembalikan kolom lama
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('booking_time')->nullable()->after('booking_end_time');
        });

        // Reset kolom baru
        DB::table('appointments')->update(['booking_start_time' => null, 'booking_end_time' => null]);

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });
    }
};
