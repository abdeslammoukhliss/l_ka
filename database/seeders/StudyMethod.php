<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyMethod extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('study_methods')->insert([
            'name' => 'local',
        ]);
        DB::table('study_methods')->insert([
            'name' => 'remote',
        ]);
        DB::table('study_methods')->insert([
            'name' => 'hybrid',
        ]);
    }
}
