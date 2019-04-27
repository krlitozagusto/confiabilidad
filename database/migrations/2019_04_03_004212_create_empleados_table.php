<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identificacion')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('celular');
            $table->string('direccion');
            $table->string('email');
            $table->enum('estado',['Activo','Inactivo']);
            $table->bigInteger('campo_id')->unsigned();
            $table->bigInteger('cargo_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('campo_id')->references('id')->on('campos')->onDelete('restrict');
            $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::dropIfExists('empleados');
    }
}
