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
                'tag'=>'XL-P-102-1-1-1',
                'descripcion'=>'descripción 10061895-1-1-1',
                'sistema_id'=>1
            ]
        );
        DB::table('ubicacion_tecnicas')->insert($rows);
    }
}
