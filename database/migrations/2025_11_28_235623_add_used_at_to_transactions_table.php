<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom used_at pada tabel transactions.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Gunakan dateTime agar kompatibel dengan semua MySQL/MariaDB versi lama
            $table->dateTime('used_at')->nullable()->after('payment_status');
        });
    }

    /**
     * Hapus kolom used_at jika rollback.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }
};
