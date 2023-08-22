<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shifts')->insert([
            'name' => 'morning',
        ]);
        DB::table('shifts')->insert([
            'name' => 'evening',
        ]);
        DB::table('shifts')->insert([
            'name' => 'night',
        ]);
    }
}
