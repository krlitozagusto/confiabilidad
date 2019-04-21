<?php

use Illuminate\Database\Seeder;

class TipoMantenimientosSeeder extends Seeder
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
                'nombre'=>'Tipo mantenimiento 1'
            ],
            [
                'id' => 2,
                'nombre'=>'Tipo mantenimiento 2'
            ]
        );
        DB::table('tipo_mantenimientos')->insert($rows);
    }
}
