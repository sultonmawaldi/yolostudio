<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();

            // kode unik utk JS & backend
            $table->string('code')->unique();
            // contoh: extra_person, extra_time, photobox_bundle

            $table->string('name');

            // harga satuan (AMAN dari manipulasi frontend)
            $table->unsignedInteger('price');

            // satuan add-on
            $table->enum('unit', ['person', 'minute', 'item'])->default('item');

            // batas maksimal (contoh: waktu max 5 menit)
            $table->unsignedInteger('max_qty')->nullable();

            // aktif / nonaktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
