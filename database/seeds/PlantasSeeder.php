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
                'centro_emplazamiento'=>'10061895-1',
                'descripcion'=>'XL-P-102-1',
                'campo_id'=>1
            ]
        );
        DB::table('plantas')->insert($rows);
    }
}
