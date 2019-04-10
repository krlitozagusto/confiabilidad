<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCamposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('centro')->unique();
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('numero_equipo_id')->unsigned();
            $table->bigInteger('contrato_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->foreign('numero_equipo_id')->references('id')->on('numero_equipos')->onDelete('restrict');
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('restrict');
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
        Schema::dropIfExists('campos');
    }
}
