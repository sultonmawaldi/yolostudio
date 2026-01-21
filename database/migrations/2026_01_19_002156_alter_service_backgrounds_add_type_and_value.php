<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_backgrounds', function (Blueprint $table) {

            // Tambahan struktur baru (BEST PRACTICE)
            $table->enum('type', ['color', 'image'])
                ->default('color')
                ->after('name');

            $table->string('value')
                ->after('type');

            // Hapus kolom lama yang bikin bug
            if (Schema::hasColumn('service_backgrounds', 'image')) {
                $table->dropColumn('image');
            }

            // Index tambahan
            $table->index(['service_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('service_backgrounds', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->dropColumn(['type', 'value']);
        });
    }
};
