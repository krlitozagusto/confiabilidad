<?php

use Illuminate\Database\Seeder;

class PlantasSeeder extends Seeder
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
                'nombre'=>'Planta 1',
                'emplazamiento'=>'emplaza1',
                'tag_id'=>2,
                'numero_equipo_id'=>2,
                'campo_id'=>1
            ]
        );
        DB::table('plantas')->insert($rows);
    }
}
