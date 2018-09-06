<?php

use Illuminate\Support\Facades\Schema;
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
            $table->string('name', 25);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('personalImageUrl')->nullable()->default('https://thumbs.dreamstime.com/b/user-icon-orange-round-sign-vector-illustration-isolated-white-background-user-icon-orange-round-sign-112808533.jpg');
            $table->string('city', 20)->nullable();
            $table->string('phone', 11);
            $table->string('address', 100)->nullable();
            $table->boolean('admin')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
