<?php

use Illuminate\Database\Seeder;

class EquiposSeeder extends Seeder
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
                'nombre'=>'equipo 1',
                'denominacion'=>'ddeq01',
                'descripcion'=>'EQ01dd01',
                'tag'=>'XL-P-102-1-1-1-EQ1',
                'numero_equipo'=>'10061895-1-1-1-EQ1',
                'valoracion_ram_id'=>1,
                'centro_costo_id'=>1,
                'sistema_id'=>1
            ],
            [
                'id' => 2,
                'nombre'=>'equipo 2',
                'denominacion'=>'ddeq02',
                'descripcion'=>'EQ02dd02',
                'tag'=>'XL-P-102-1-1-1-EQ2',
                'numero_equipo'=>'10061895-1-1-1-EQ2',
                'valoracion_ram_id'=>2,
                'centro_costo_id'=>1,
                'sistema_id'=>1
            ]
        );
        DB::table('equipos')->insert($rows);
    }
}
