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
                'tag_id'=>1,
                'numero_equipo_id'=>1,
                'contrato_id'=>1
            ]
        );
        DB::table('campos')->insert($rows);
    }
}
