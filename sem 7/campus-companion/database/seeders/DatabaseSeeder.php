<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ClassRoomSeeder::class,
            UserSeeder::class,
            TimetableSeeder::class,
            NoticeSeeder::class,
            EventSeeder::class,
            ComplaintSeeder::class,
            FloorSeeder::class,
        ]);
    }
}
