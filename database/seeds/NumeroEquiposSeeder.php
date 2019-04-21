<?php

use Illuminate\Database\Seeder;

class NumeroEquiposSeeder extends Seeder
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
                'codigo'=>'10061895'
            ],
            [
                'id' => 2,
                'codigo'=>'10061895-1'
            ],
            [
                'id' => 3,
                'codigo'=>'10061895-1-1'
            ],
            [
                'id' => 4,
                'codigo'=>'10061895-1-1-1'
            ],
            [
                'id' => 5,
                'codigo'=>'10061895-1-1-1-EQ1'
            ],
            [
                'id' => 6,
                'codigo'=>'10061895-1-1-1-EQ2'
            ]
        );
        DB::table('numero_equipos')->insert($rows);
    }
}
