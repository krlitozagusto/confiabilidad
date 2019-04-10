<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('cantidad');
            $table->float('valor');
            $table->text('observaciones');
            $table->bigInteger('evento_id')->unsigned()->nullable();
            $table->bigInteger('tipo_gasto_id')->unsigned()->nullable();
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('restrict');
            $table->foreign('tipo_gasto_id')->references('id')->on('tipo_gastos')->onDelete('restrict');
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
        Schema::dropIfExists('gastos');
    }
}
