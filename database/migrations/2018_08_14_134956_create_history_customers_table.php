<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id'); 
            $table->string('user_status', 50);
            $table->double('total_usage');            
            $table->double('allocated_quota');            
            $table->timestamps();
        });

        Schema::table('history_customers', function($table) {
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('history_customers');
    }
}
