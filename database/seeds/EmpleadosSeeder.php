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
                'nombre'=>'Jose Humberto Torres',
                'celular'=>'31258541258',
                'direccion'=>'calle 42 12 05',
                'email'=>'torreslombana@gmail.com',
                'estado'=>'Activo',
                'campo_id'=>1,
                'cargo_id'=>1,
                'user_id'=>2
            ]
        );
        DB::table('empleados')->insert($rows);
    }
}
