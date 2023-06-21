<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'designation' => 'digital marketing',
        ]);
        DB::table('categories')->insert([
            'designation' => 'software development',
        ]);
        DB::table('categories')->insert([
            'designation' => 'graphic design',
        ]);
    }
}
