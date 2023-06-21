<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'full_name' => 'mohammed',
            'email' => 'mohammed@gmail.com',
            'password' => bcrypt('000000'),
            'phone_number' => '0666666666',
            'gender' => 1,
            'city' => 'agadir',
            'role' => 1
        ]);
    }
}
