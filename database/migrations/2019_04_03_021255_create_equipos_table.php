<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('denominacion');
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('numero_equipo_id')->unsigned();
            $table->bigInteger('valoracion_ram_id')->unsigned();
            $table->bigInteger('centro_costo_id')->unsigned();
            $table->bigInteger('ubicacion_tecnica_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->foreign('numero_equipo_id')->references('id')->on('numero_equipos')->onDelete('restrict');
            $table->foreign('valoracion_ram_id')->references('id')->on('valoracion_rams')->onDelete('restrict');
            $table->foreign('centro_costo_id')->references('id')->on('centro_costos')->onDelete('restrict');
            $table->foreign('ubicacion_tecnica_id')->references('id')->on('ubicacion_tecnicas')->onDelete('restrict');
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
        Schema::dropIfExists('equipos');
    }
}
