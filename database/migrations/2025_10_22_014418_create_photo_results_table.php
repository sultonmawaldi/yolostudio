<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration
     */
    public function up(): void
    {
        Schema::create('photo_results', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke transaksi
            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->onDelete('cascade');

            // Lokasi file di storage/public/...
            $table->string('file_path');

            // Nama file (opsional, memudahkan penamaan)
            $table->string('file_name')->nullable();

            // URL publik (opsional, jika kamu generate CDN)
            $table->string('public_url')->nullable();

            // Metadata tambahan (misal ukuran, format)
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable();

            // Waktu upload
            $table->timestamp('uploaded_at')->nullable();

            $table->softDeletes(); // Bisa hapus sementara
            $table->timestamps();  // created_at & updated_at
        });
    }

    /**
     * Hapus tabel jika di-rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_results');
    }
};
