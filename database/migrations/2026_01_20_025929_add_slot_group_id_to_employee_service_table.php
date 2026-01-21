<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_service', function (Blueprint $table) {
            $table->unsignedBigInteger('slot_group_id')
                ->nullable()
                ->after('service_id');

            $table->foreign('slot_group_id')
                ->references('id')
                ->on('slot_groups')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('employee_service', function (Blueprint $table) {
            $table->dropForeign(['slot_group_id']);
            $table->dropColumn('slot_group_id');
        });
    }
};
