<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('slot_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('slot_groups', 'working_hours')) {
                $table->json('working_hours')
                    ->nullable()
                    ->after('end_time')
                    ->comment('Jam kerja / istirahat karyawan dalam format JSON');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slot_groups', function (Blueprint $table) {
            if (Schema::hasColumn('slot_groups', 'working_hours')) {
                $table->dropColumn('working_hours');
            }
        });
    }
};
