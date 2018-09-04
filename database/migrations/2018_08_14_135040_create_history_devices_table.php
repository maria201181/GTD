<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('device_id');
            $table->string('system_settings_backed_up', 100);
            /*last_backup*/
            $table->string("last_backup_status");
            $table->dateTime('backup_start_time');
            $table->dateTime('backup_end_time');
            $table->double('bytes_transferred');
            /*first_backup*/
            $table->string('first_backup_status');
            $table->double('first_backup_size');
            $table->string('time_taken');

            $table->timestamps();
        });

        Schema::table('history_devices', function($table) {
            $table->foreign('device_id')->references('id')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('history_devices');
    }
}
