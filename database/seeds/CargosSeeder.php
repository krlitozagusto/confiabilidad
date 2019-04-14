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
                'nombre'=> 'CMMS',
                'area_id'=>1
            ]
        );
        DB::table('cargos')->insert($rows);
    }
}
