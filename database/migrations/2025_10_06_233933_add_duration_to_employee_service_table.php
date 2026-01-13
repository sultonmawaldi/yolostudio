<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employee_service', function (Blueprint $table) {
            $table->integer('duration')->nullable()->after('service_id');
        });
    }

    public function down()
    {
        Schema::table('employee_service', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};

