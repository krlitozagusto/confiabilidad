<?php

use Illuminate\Database\Seeder;

class CentroCostosSeeder extends Seeder
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
                'codigo'=>'CC1',
                'denominacion'=>'Centro de equipos'
            ]
        );
        DB::table('centro_costos')->insert($rows);
    }
}
