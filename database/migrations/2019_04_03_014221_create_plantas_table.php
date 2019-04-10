<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plantas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('emplazamiento')->unique();
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('numero_equipo_id')->unsigned();
            $table->bigInteger('campo_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->foreign('numero_equipo_id')->references('id')->on('numero_equipos')->onDelete('restrict');
            $table->foreign('campo_id')->references('id')->on('campos')->onDelete('restrict');
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
        Schema::dropIfExists('plantas');
    }
}
