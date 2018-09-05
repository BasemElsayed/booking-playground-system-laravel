<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('playground_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('playground_id')->references('id')->on('playgrounds')->onDelete('cascade');
            $table->date('bookedDateFrom');
            $table->date('bookedDateTo');
            $table->time('bookedTimeFrom');
            $table->time('bookedTimeTo');
            $table->double('price');
            $table->boolean('approved')->default(0);
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
        Schema::dropIfExists('bookings');
    }
}

//2-9-2018  02:00PM
//5-9-2018  10:00PM
//5-2 = 3
//9-9 = 0
//2018-2018 = 0;




