<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('consultations_statuses')->insert([
            'name' => 'pending',
        ]);
        DB::table('consultations_statuses')->insert([
            'name' => 'confirmed',
        ]);
        DB::table('consultations_statuses')->insert([
            'name' => 'done',
        ]);
        DB::table('consultations_statuses')->insert([
            'name' => 'rejected',
        ]);
    }
}
