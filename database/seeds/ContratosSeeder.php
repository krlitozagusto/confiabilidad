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
                'numero'=>'1038050A',
                'descripcion'=>'Mantenimiento Facilidades',
                'fecha'=>'2018-06-30',
                'plazo_dias'=>2555
            ]
        );
        DB::table('contratos')->insert($rows);
    }
}
