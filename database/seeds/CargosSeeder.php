<?php

use Illuminate\Database\Seeder;

class CargosSeeder extends Seeder
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
                'nombre'=> ' Ingeniero de Confiabilidad',
                'area_id'=> 2
            ],[
                'id' => 2,
                'nombre'=> ' Analista de Confiabilidad',
                'area_id'=> 2
            ],[
                'id' => 3,
                'nombre'=> ' Profesional Confiabilidad',
                'area_id'=> 2
            ],[
                'id' => 4,
                'nombre'=> ' Gerente O&M',
                'area_id'=> 2
            ],[
                'id' => 5,
                'nombre'=> ' Analista CMMS',
                'area_id'=> 2
            ],[
                'id' => 6,
                'nombre'=> ' Ingeniero CMMS',
                'area_id'=> 2
            ]
        );
        DB::table('cargos')->insert($rows);
    }
}
