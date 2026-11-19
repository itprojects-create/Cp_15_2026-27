<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::updateOrCreate(
            ['email' => 'admin@campus.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // TIMETABLE MANAGER / COMMITTEE
        User::updateOrCreate(
            ['email' => 'timetable@campus.com'],
            [
                'name' => 'Timetable Manager',
                'password' => Hash::make('password'),
                'role' => 'timetable_commity',
            ]
        );

        User::updateOrCreate(
            ['email' => 'committee@campus.com'],
            [
                'name' => 'Timetable Committee',
                'password' => Hash::make('password'),
                'role' => 'timetable_commity',
            ]
        );

        foreach (Department::all() as $dept) {

            // TEACHERS
            for ($i = 1; $i <= 2; $i++) {
                User::updateOrCreate(
                    ['email' => strtolower(str_replace(' ', '', $dept->name))."_teacher{$i}@mail.com"],
                    [
                        'name' => "{$dept->name} Teacher {$i}",
                        'password' => Hash::make('password'),
                        'role' => 'teacher',
                        'department_id' => $dept->id,
                    ]
                );
            }

            // STUDENTS
            $classes = ClassRoom::where('department_id', $dept->id)->get();

            foreach ($classes as $class) {

                for ($i = 1; $i <= 10; $i++) {

                    $email = strtolower(
                        str_replace(' ', '', $dept->name).'_'.
                        $class->year.$class->name.'_'.$i
                    ).'@student.com';

                    User::updateOrCreate(
                        ['email' => $email],
                        [
                            'name' => "{$class->year}{$class->name} Student {$i}",
                            'password' => Hash::make('password'),
                            'role' => 'student',
                            'department_id' => $dept->id,
                            'class_room_id' => $class->id,
                        ]
                    );
                }
            }
        }
    }
}
