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
        Schema::table('employees', function (Blueprint $table) {
            // Pastikan kolomnya ada sebelum drop
            if (Schema::hasColumn('employees', 'slot_duration')) {
                $table->dropColumn('slot_duration');
            }

            if (Schema::hasColumn('employees', 'break_duration')) {
                $table->dropColumn('break_duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Balikin lagi kalau rollback
            $table->integer('slot_duration')->nullable();
            $table->integer('break_duration')->nullable();
        });
    }
};
