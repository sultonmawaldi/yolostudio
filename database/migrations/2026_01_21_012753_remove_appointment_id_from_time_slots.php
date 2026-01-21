<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            if (Schema::hasColumn('time_slots', 'appointment_id')) {

                // 1️⃣ DROP foreign key dulu
                $table->dropForeign(['appointment_id']);

                // 2️⃣ Baru drop kolom
                $table->dropColumn('appointment_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->foreignId('appointment_id')
                ->nullable()
                ->after('is_booked')
                ->constrained('appointments')
                ->nullOnDelete();
        });
    }
};
