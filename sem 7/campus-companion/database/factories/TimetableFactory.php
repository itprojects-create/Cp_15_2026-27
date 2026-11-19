<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ClassRoom;

class TimetableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'day' => fake()->randomElement([
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'
            ]),
            'time_slot' => fake()->randomElement([
                '08:30-09:30',
                '09:30-10:30',
                '10:30-11:30',
                '11:30-12:30',
                '01:30-02:30',
                '02:30-03:30',
            ]),
            'subject' => fake()->randomElement([
                'AOA', 'MLDL', 'ITIL', 'ADM', 'ECOM'
            ]),
            'faculty' => fake()->name(),
            'room' => 'Room ' . rand(101, 305),
            'class_room_id' => ClassRoom::inRandomOrder()->first()?->id,
        ];
    }
}
