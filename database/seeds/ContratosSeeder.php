<?php

use Illuminate\Database\Seeder;

class ContratosSeeder extends Seeder
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
                'numero'=>'CT2541',
                'descripcion'=>'Casanare',
                'fecha'=>'2018-05-23',
                'plazo_dias'=>730
            ]
        );
        DB::table('contratos')->insert($rows);
    }
}
