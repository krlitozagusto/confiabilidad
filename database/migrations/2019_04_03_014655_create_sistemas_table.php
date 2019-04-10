<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSistemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sistemas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('numero_equipo_id')->unsigned();
            $table->bigInteger('planta_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->foreign('numero_equipo_id')->references('id')->on('numero_equipos')->onDelete('restrict');
            $table->foreign('planta_id')->references('id')->on('plantas')->onDelete('restrict');
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
        Schema::dropIfExists('sistemas');
    }
}
