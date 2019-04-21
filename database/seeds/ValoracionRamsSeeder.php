<?php

use Illuminate\Database\Seeder;

class ValoracionRamsSeeder extends Seeder
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
                'codigo'=>'vr01',
                'nombre'=>'Valoración R1'
            ],
            [
                'id' => 2,
                'codigo'=>'vr02',
                'nombre'=>'Valoración R2'
            ]
        );
        DB::table('valoracion_rams')->insert($rows);
    }
}
