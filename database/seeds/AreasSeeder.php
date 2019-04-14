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
                'codigo'=>'YINS',
                'nombre'=> 'Instrumentación'
            ]
        );
        DB::table('areas')->insert($rows);
    }
}
