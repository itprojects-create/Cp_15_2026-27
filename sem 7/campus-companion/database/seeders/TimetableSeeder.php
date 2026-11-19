<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timetable;
use App\Models\ClassRoom;
use App\Models\User;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        // clear old data
        Timetable::truncate();

        $classes = ClassRoom::all();
        $teachers = User::where('role', 'teacher')->get();

        if ($teachers->count() === 0) {
            throw new \Exception('No teachers found. Seed teachers first.');
        }

        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

        $slots = [
            '08:30-09:30',
            '09:30-10:30',
            '10:30-11:30',
            '11:30-12:30',
            '12:30-01:30', // break
            '01:30-02:30',
            '02:30-03:30',
        ];

        $subjects = [
            'DBMS','OS','CN','AI','ML','Maths','DSA','SE','Cloud'
        ];

        foreach ($classes as $classIndex => $class) {

            foreach ($days as $dayIndex => $day) {

                foreach ($slots as $slotIndex => $slot) {

                    // BREAK SLOT
                    if ($slot === '12:30-01:30') {
                        Timetable::create([
                            'class_room_id' => $class->id,
                            'day' => $day,
                            'time_slot' => $slot,
                            'subject' => 'BREAK',
                            'faculty_id' => null,
                            'room' => '-',
                        ]);
                        continue;
                    }

                    // rotate teacher (balanced distribution)
                    $teacher = $teachers[
                        ($classIndex + $dayIndex + $slotIndex) % $teachers->count()
                    ];

                    Timetable::create([
                        'class_room_id' => $class->id,
                        'day' => $day,
                        'time_slot' => $slot,
                        'subject' => $subjects[array_rand($subjects)],
                        'faculty_id' => $teacher->id,
                        'room' => rand(101, 599),
                    ]);
                }
            }
        }
    }
}
