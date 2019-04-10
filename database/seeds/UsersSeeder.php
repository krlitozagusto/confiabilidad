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
            array(
                'name'=>'krlitozagusto',
                'email'=>'sandovalcarlosaugusto@gmail.com',
                'password'=>bcrypt('Admin12345')
            )
        );
        DB::table('users')->insert($rows);
    }
}
