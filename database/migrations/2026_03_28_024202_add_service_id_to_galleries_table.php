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
        Schema::table('galleries', function (Blueprint $table) {

            // Tambah kolom service_id setelah title
            $table->foreignId('service_id')
                ->nullable()
                ->after('title')
                ->constrained('services') // relasi ke tabel services
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {

            // Drop foreign key dulu
            $table->dropForeign(['service_id']);

            // Baru drop kolom
            $table->dropColumn('service_id');
        });
    }
};
