<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('studio_id')
                ->after('service_id')
                ->nullable()
                ->constrained('studios')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['studio_id']);
            $table->dropColumn('studio_id');
        });
    }
};
