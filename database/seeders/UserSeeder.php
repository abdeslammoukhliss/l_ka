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
            'full_name' => 'mohammed abotrika',
            'email' => 'mohammed@gmail.com',
            'password' => bcrypt('000000'),
            'phone_number' => '0666666666',
            'gender' => 1,
            'city' => 'agadir',
            'role' => 1,
            'email_verified_at' => now(),
            'registration_token' => Str::random(64)
        ]);

        DB::table('users')->insert([
            'full_name' => 'amin lakjaa',
            'email' => 'lakjaa@gmail.com',
            'password' => bcrypt('000000'),
            'phone_number' => '0777777777',
            'gender' => 1,
            'city' => 'tarodant',
            'role' => 2,
            'email_verified_at' => now(),
            'registration_token' => Str::random(64)
        ]);
        DB::table('users')->insert([
            'full_name' => 'salma alaoui',
            'email' => 'alaoui@gmail.com',
            'password' => bcrypt('000000'),
            'phone_number' => '0888888888',
            'gender' => 2,
            'city' => 'rabat',
            'role' => 2,
            'email_verified_at' => now(),
            'registration_token' => Str::random(64)
        ]);
    }
}
