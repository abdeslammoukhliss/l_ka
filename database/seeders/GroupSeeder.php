<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            'name' => 'default',
            'course' => 1
        ]);
        DB::table('groups')->insert([
            'name' => 'morning',
            'course' => 1
        ]);
        DB::table('groups')->insert([
            'name' => 'night',
            'course' => 1
        ]);
        
        DB::table('groups')->insert([
            'name' => 'default',
            'course' => 2
        ]);
        DB::table('groups')->insert([
            'name' => 'morning',
            'course' => 2
        ]);
        DB::table('groups')->insert([
            'name' => 'night',
            'course' => 2
        ]);
    }
}
