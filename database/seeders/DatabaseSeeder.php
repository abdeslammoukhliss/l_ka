<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            ConsultationStatusSeeder::class,
            StudyMethodSeeder::class,
            ShiftSeeder::class,
            UserSeeder::class,
            CourseSeeder::class,
            GroupSeeder::class,
            ModuleSeeder::class,
            ProjectSeeder::class,
            ChapterSeeder::class,
            // StudentGroupSeeder::class,
            TeacherModuleSeeder::class
        ]);
    }
}
