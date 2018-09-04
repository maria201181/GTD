<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('second_surname', 100);
            $table->integer('rut');
            $table->char('dv', 1);
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('status');
            $table->integer('company_id')->unsigned();
            $table->integer('profile_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('profile_id')->references('id')->on('profiles');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
