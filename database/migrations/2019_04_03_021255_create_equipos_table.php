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
            $table->string('descripcion');
            $table->string('tag')->unique();
            $table->string('numero_equipo')->unique();
            $table->bigInteger('valoracion_ram_id')->unsigned();
            $table->bigInteger('centro_costo_id')->unsigned();
            $table->bigInteger('sistema_id')->unsigned();
            $table->foreign('valoracion_ram_id')->references('id')->on('valoracion_rams')->onDelete('restrict');
            $table->foreign('centro_costo_id')->references('id')->on('centro_costos')->onDelete('restrict');
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('restrict');
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
