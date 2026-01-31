<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricelists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('price');

            $table->json('features')->nullable();
            $table->string('category')->nullable();

            $table->string('button_text')->default('Booking');
            $table->string('button_link')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricelists');
    }
};
