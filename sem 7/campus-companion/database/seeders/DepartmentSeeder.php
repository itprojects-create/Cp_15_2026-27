<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Computer Engineering',
            'Information Technology',
            'Electronics',
            'Artificial Intelligence',
            'Robotics'
        ];

        foreach ($departments as $name) {
            Department::create(['name' => $name]);
        }
    }
}
