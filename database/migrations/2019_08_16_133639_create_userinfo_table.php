<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userinfo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('surname');
            $table->string('patronymic');
            $table->string('phone');
            $table->string('coords');
            $table->string('corp_email');
            $table->string('city');
            $table->string('street');
            $table->string('building');
            $table->string('office');
            $table->string('terminal');
            $table->timestamp('last_sale');
            $table->integer('points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userinfo');
    }
}
