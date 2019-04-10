<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventoModoFallaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evento_modo_falla', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('evento_id')->unsigned();
            $table->bigInteger('modo_falla_id')->unsigned();
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('restrict');
            $table->foreign('modo_falla_id')->references('id')->on('modo_fallas')->onDelete('restrict');
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
        Schema::dropIfExists('evento_modo_falla');
    }
}
