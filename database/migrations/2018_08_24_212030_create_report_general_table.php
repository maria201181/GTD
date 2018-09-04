<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_general', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period', 6);
            $table->integer('customers_subscribed');
            $table->integer('customers_active');
            $table->integer('customers_activo_used');
            $table->integer('customers_activo_unused');
            $table->integer('customers_unsubscribed');
            $table->integer('company_id');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_general');
    }
}
