<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenTrabajosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_trabajos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_orden')->unique();
            $table->text('descripcion');
            $table->string('numero_aviso')->unique();
            $table->bigInteger('puesto_trabajo_id')->unsigned();
            $table->bigInteger('evento_id')->unsigned();
            $table->foreign('puesto_trabajo_id')->references('id')->on('puesto_trabajos')->onDelete('restrict');
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('restrict');
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
        Schema::dropIfExists('orden_trabajos');
    }
}
