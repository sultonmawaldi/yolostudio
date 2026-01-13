<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->time('booking_start_time')->nullable();
            $table->time('booking_end_time')->nullable();
        });

        // Isi kolom baru berdasarkan kolom lama booking_time
        $appointments = DB::table('appointments')->get();
        foreach ($appointments as $a) {
            if ($a->booking_time) {
                $times = explode(' - ', $a->booking_time);
                if (count($times) == 2) {
                    $start = \Carbon\Carbon::createFromFormat('g:i A', trim($times[0]))->format('H:i:s');
                    $end   = \Carbon\Carbon::createFromFormat('g:i A', trim($times[1]))->format('H:i:s');
                    DB::table('appointments')->where('id', $a->id)->update([
                        'booking_start_time' => $start,
                        'booking_end_time' => $end,
                    ]);
                }
            }
        }

        // Setelah selesai, bisa drop kolom lama
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('booking_time');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('booking_time')->nullable();
            $table->dropColumn(['booking_start_time', 'booking_end_time']);
        });
    }
};

