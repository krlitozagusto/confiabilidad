<?php

use Illuminate\Database\Seeder;

class EmpleadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
            [
                'id' => 1,
                'identificacion'=>'9430885',
                'nombre'=>'Jose Humberto ',
                'apellido'=>'Torres Lombana',
                'celular'=>'3124160394',
                'direccion'=>'Yopal -Casanare',
                'email'=>'humberto.torres@stork.com',
                'estado'=>'Activo',
                'campo_id'=>1,
                'cargo_id'=>2
            ],[
                'id' => 2,
                'identificacion'=>'9430886',
                'nombre'=>'Ruben Dario',
                'apellido'=>'Wallys',
                'celular'=>'3213430036',
                'direccion'=>'Yopal -Casanare',
                'email'=>'ruben.wallys@stork.com',
                'estado'=>'Activo',
                'campo_id'=>2,
                'cargo_id'=>2
            ],[
                'id' => 3,
                'identificacion'=>'9430887',
                'nombre'=>'Raul Alberto',
                'apellido'=>'Culma',
                'celular'=>'3168675558',
                'direccion'=>'Yopal -Casanare',
                'email'=>'raul.culma@stork.com',
                'estado'=>'Activo',
                'campo_id'=>1,
                'cargo_id'=>2
            ],[
                'id' => 4,
                'identificacion'=>'1118542092',
                'nombre'=>'Larrissa ',
                'apellido'=>'Avella',
                'celular'=>'3138531873',
                'direccion'=>'Yopal -Casanare',
                'email'=>'larissa.avella@stork.com',
                'estado'=>'Activo',
                'campo_id'=>1,
                'cargo_id'=>2
            ]
        );
        DB::table('empleados')->insert($rows);
    }
}
