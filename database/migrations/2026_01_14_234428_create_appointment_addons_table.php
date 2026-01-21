<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_addons', function (Blueprint $table) {
            $table->id();

            // relasi ke appointment EXISTING
            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->cascadeOnDelete();

            // relasi ke addons
            $table->foreignId('addon_id')
                ->constrained()
                ->cascadeOnDelete();

            // snapshot harga saat transaksi
            $table->unsignedInteger('price');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('subtotal');

            $table->timestamps();

            // 1 appointment 1 addon (tidak dobel)
            $table->unique(['appointment_id', 'addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_addons');
    }
};
