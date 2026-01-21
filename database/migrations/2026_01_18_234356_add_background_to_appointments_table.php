<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('background_id')
                ->nullable()
                ->after('service_id')
                ->constrained('service_backgrounds')
                ->nullOnDelete();

            // Snapshot nama (aman untuk histori)
            $table->string('background_name')->nullable()->after('background_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['background_id']);
            $table->dropColumn(['background_id', 'background_name']);
        });
    }
};
