<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slot_groups', function (Blueprint $table) {

            if (!Schema::hasColumn('slot_groups', 'start_time')) {
                $table->time('start_time')
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('slot_groups', 'end_time')) {
                $table->time('end_time')
                    ->nullable()
                    ->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('slot_groups', function (Blueprint $table) {

            if (Schema::hasColumn('slot_groups', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (Schema::hasColumn('slot_groups', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }
};
