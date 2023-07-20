<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('students_groups')->insert([
            'student' => 4,
            'group' => 2,
            'registration_date' => now()
        ]);
        DB::table('students_groups')->insert([
            'student' => 5,
            'group' => 2,
            'registration_date' => now()
        ]);

        DB::table('students_groups')->insert([
            'student' => 4,
            'group' => 6,
            'registration_date' => now()
        ]);
        DB::table('students_groups')->insert([
            'student' => 5,
            'group' => 6,
            'registration_date' => now()
        ]);
    }
}
