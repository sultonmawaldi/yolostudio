<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_shapes', function (Blueprint $table) {
            $table->id();

            // Relasi ke addons
            $table->foreignId('addon_id')
                ->constrained('addons')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Relasi ke addon utama');

            $table->string('shape_name', 50)
                ->comment('Nama bentuk addon, misal oval, bulat, persegi panjang');

            $table->unsignedInteger('price')->nullable()
                ->comment('Opsional, harga khusus untuk bentuk ini');

            $table->unsignedInteger('stock')->nullable()
                ->comment('Opsional, jumlah tersedia untuk bentuk ini');

            $table->string('image', 255)->nullable()
                ->comment('Opsional, gambar khusus bentuk addon');

            $table->boolean('is_active')->default(true)
                ->comment('Status aktif atau tidak');

            $table->integer('sort_order')->default(0)
                ->comment('Urutan tampil di frontend');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_shapes');
    }
};
