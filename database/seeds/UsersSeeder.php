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
                'name'=>'humbertoTorres',
                'email'=>'torreslombana@gmail.com',
                'password'=>bcrypt('Admin12345'),
                'avatar'=>'avatar02.png'
            ]
        );
        DB::table('users')->insert($rows);
    }
}
