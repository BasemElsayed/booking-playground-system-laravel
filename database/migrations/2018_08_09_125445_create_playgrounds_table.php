<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->double('price');
            $table->string('address');
            $table->double('area');
            $table->string('imageURL')->nullable()->default('https://www.berjayahotel.com/sites/default/files/styles/gallery_slide/public/timessquare_55.jpg');
            $table->time('avaiableFrom');
            $table->time('avaiableTo');
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
        Schema::dropIfExists('playgrounds');
    }
}
