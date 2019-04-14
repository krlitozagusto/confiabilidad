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
            ]
        );
        DB::table('numero_equipos')->insert($rows);
    }
}
