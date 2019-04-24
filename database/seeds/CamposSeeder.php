<?php

use Illuminate\Database\Seeder;

class CamposSeeder extends Seeder
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
                'nombre'=>'Cupiagua',
                'centro'=>'1064',
                'tag'=>'XL-P-102',
                'numero_equipo'=>'10061895',
                'contrato_id'=>1
            ]
        );
        DB::table('campos')->insert($rows);
    }
}
