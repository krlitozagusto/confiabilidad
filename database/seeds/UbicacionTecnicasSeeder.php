<?php

use Illuminate\Database\Seeder;

class UbicacionTecnicasSeeder extends Seeder
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
                'nombre'=>'Ubicación 1',
                'tag_id'=>4,
                'numero_equipo_id'=>4,
                'sistema_id'=>1
            ]
        );
        DB::table('ubicacion_tecnicas')->insert($rows);
    }
}
