<?php

use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
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
                'codigo'=>'XL-P-102'
            ],
            [
                'id' => 2,
                'codigo'=>'XL-P-102-1'
            ],
            [
                'id' => 3,
                'codigo'=>'XL-P-102-1-1'
            ],
            [
                'id' => 4,
                'codigo'=>'XL-P-102-1-1-1'
            ],
            [
                'id' => 5,
                'codigo'=>'XL-P-102-1-1-1-EQ1'
            ],
            [
                'id' => 6,
                'codigo'=>'XL-P-102-1-1-1-EQ2'
            ]
        );
        DB::table('tags')->insert($rows);
    }
}
