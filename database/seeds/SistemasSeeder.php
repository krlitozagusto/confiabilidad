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
                'tag'=>'XL-P-102-1-1',
                'descripcion'=>'10061895-1-1',
                'planta_id'=>1
            ]
        );
        DB::table('sistemas')->insert($rows);
    }
}
