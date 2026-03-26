<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {

            // hapus foreign key dulu
            $table->dropForeign(['service_id']);

            // baru hapus kolom
            $table->dropColumn('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {

            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();
        });
    }
};
