<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanMantenimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_mantenimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_plan');
            $table->string('frecuencia');
            $table->text('descripcion');
            $table->bigInteger('equipo_id')->unsigned();
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('restrict');

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
        Schema::dropIfExists('plan_mantenimientos');
    }
}
