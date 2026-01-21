<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_backgrounds', function (Blueprint $table) {
            $table->id();

            // Relasi ke service
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            // Data background
            $table->string('name');                 // Putih, Hitam, Grey, dll
            $table->string('image')->nullable();    // preview (optional)
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Index tambahan (opsional tapi recommended)
            $table->index(['service_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_backgrounds');
    }
};
