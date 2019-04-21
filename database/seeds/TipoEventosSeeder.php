<?php

use Illuminate\Database\Seeder;

class TipoEventosSeeder extends Seeder
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
                'nombre'=>'Tipo evento 1'
            ],
            [
                'id' => 2,
                'nombre'=>'Tipo evento 2'
            ]
        );
        DB::table('tipo_eventos')->insert($rows);
    }
}
