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
                'tag_id'=>5,
                'numero_equipo_id'=>5,
                'valoracion_ram_id'=>1,
                'centro_costo_id'=>1,
                'ubicacion_tecnica_id'=>1
            ],
            [
                'id' => 2,
                'nombre'=>'equipo 2',
                'denominacion'=>'ddeq02',
                'descripcion'=>'EQ02dd02',
                'tag_id'=>6,
                'numero_equipo_id'=>6,
                'valoracion_ram_id'=>2,
                'centro_costo_id'=>1,
                'ubicacion_tecnica_id'=>1
            ]
        );
        DB::table('equipos')->insert($rows);
    }
}
