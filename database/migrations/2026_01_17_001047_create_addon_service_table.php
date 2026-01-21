<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addon_service', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('addon_id')
                ->constrained()
                ->cascadeOnDelete();

            // Pastikan tidak ada duplikasi addon pada service yang sama
            $table->unique(['service_id', 'addon_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_service');
    }
};
