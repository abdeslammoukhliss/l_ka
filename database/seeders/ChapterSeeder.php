<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chapters')->insert([
            'name' => 'html and css',
            'status' => 0,
            'module' => 1,
        ]);
        DB::table('chapters')->insert([
            'name' => 'javascript basics',
            'status' => 0,
            'module' => 1,
        ]);
        
        DB::table('chapters')->insert([
            'name' => 'sql databases',
            'status' => 0,
            'module' => 2,
        ]);
        DB::table('chapters')->insert([
            'name' => 'php basics',
            'status' => 0,
            'module' => 2,
        ]);
        
        DB::table('chapters')->insert([
            'name' => 'colors theory',
            'status' => 0,
            'module' => 3,
        ]);
        DB::table('chapters')->insert([
            'name' => 'spacing and positionning basics',
            'status' => 0,
            'module' => 3,
        ]);
        
    }
}
