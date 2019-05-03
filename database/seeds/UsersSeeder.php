<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
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
                'name'=>'krlitozagusto',
                'email'=>'sandovalcarlosaugusto@gmail.com',
                'password'=>bcrypt('Admin12345'),
                'avatar'=>'avatar01.png'
            ],
            [
                'id' => 2,
                'name'=>'raulCulma',
                'email'=>'raul.culma@stork.com',
                'password'=>bcrypt('Admin123'),
                'avatar'=>'avatar051.png'
            ]
        );
        DB::table('users')->insert($rows);
    }
}
