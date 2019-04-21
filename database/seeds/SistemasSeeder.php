<?php

use Illuminate\Database\Seeder;

class SistemasSeeder extends Seeder
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
                'nombre'=>'Sistema 1',
                'tag_id'=>3,
                'numero_equipo_id'=>3,
                'planta_id'=>1
            ]
        );
        DB::table('sistemas')->insert($rows);
    }
}
