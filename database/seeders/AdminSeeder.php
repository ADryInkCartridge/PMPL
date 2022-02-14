<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_id' => 1,
            'nama' => 'admin',
            'username'=>'admin',
            'password'=>'$2a$12$vGIB51ravON5quK6YxXJN.fEaeBJqNhB6ODhTkDa8li.JnFslUZxi',
            'role' => "Super User",
        ]);
    }
}
