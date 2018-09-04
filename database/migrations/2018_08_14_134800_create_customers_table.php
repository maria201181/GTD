<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('user_name', 100);
            $table->string('email_id', 100);            
            $table->string('profile', 50);
            $table->dateTime('added_on');
            $table->string('storage', 100);
            $table->string('plan', 100);
            $table->unsignedInteger('company_id');
            $table->timestamps();
        });

        Schema::table('customers', function($table) {
            $table->foreign('company_id')->references('id')->on('companies');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customers');
    }
}
