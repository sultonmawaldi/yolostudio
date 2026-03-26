<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->string('calendar_id')
                ->nullable()
                ->after('phone');

            $table->string('notification_email')
                ->nullable()
                ->after('calendar_id');
        });
    }

    public function down()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn(['calendar_id', 'notification_email']);
        });
    }
};
