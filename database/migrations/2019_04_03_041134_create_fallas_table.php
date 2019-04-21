<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFallasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fallas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sintoma');
            $table->string('sistema');
            $table->string('parte')->nullable();
            $table->string('accion_correctiva')->nullable();
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
        Schema::dropIfExists('fallas');
    }
}
