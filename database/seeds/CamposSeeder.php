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
                'contrato_id'=>1
            ],[
                'id' => 2,
                'nombre'=>'Cusiana',
                'centro'=>'1063',
                'contrato_id'=>1
            ],[
                'id' => 3,
                'nombre'=>'Floreña',
                'centro'=>'1065',
                'contrato_id'=>1
            ]
        );
        DB::table('campos')->insert($rows);
    }
}
