<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpactosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impactos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('total');
            $table->bigInteger('evento_id')->unsigned();
            $table->bigInteger('tipo_impacto_id')->unsigned();
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('restrict');
            $table->foreign('tipo_impacto_id')->references('id')->on('tipo_impactos')->onDelete('restrict');
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
        Schema::dropIfExists('impactos');
    }
}
