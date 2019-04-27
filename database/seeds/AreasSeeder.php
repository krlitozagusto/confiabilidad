<?php

use Illuminate\Database\Seeder;

class AreasSeeder extends Seeder
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
                'codigo'=>'1',
                'nombre'=> 'Administrativa'
            ],[
                'id' => 2,
                'codigo'=>'2',
                'nombre'=> 'Mantenimiento'
            ],[
                'id' => 3,
                'codigo'=>'3',
                'nombre'=> 'Ingenieria'
            ],[
                'id' => 4,
                'codigo'=>'4',
                'nombre'=> 'Gerencia'
            ],[
                'id' => 5,
                'codigo'=>'5',
                'nombre'=> 'Cliente'
            ]
        );
        DB::table('areas')->insert($rows);
    }
}
