<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teachers_modules')->insert([
            'teacher' => 2,
            'module' => 1,
        ]);
        
        DB::table('teachers_modules')->insert([
            'teacher' => 3,
            'module' => 2,
        ]);
        
        DB::table('teachers_modules')->insert([
            'teacher' => 2,
            'module' => 3,
        ]);
    }
}
