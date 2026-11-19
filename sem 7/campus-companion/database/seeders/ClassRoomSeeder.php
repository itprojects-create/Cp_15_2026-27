<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\Department;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        foreach ($departments as $dept) {

            foreach (['FY', 'SY', 'TY'] as $year) {

                foreach (['A', 'B'] as $division) {

                    ClassRoom::create([
                        'name' => $division,
                        'year' => $year,
                        'department_id' => $dept->id,
                    ]);
                }
            }
        }
    }
}
