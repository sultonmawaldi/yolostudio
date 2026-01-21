<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_groups', function (Blueprint $table) {
            $table->id();

            // RELASI
            $table->unsignedBigInteger('employee_id');

            // IDENTITAS SLOT
            $table->string('name'); // contoh: "Selfphoto & Pas Foto", "Photobox"

            // SLOT CONFIG
            $table->unsignedInteger('slot_duration'); // menit (30, 5, dll)
            $table->unsignedInteger('break_duration')->default(0); // menit

            $table->timestamps();

            // FK
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_groups');
    }
};
