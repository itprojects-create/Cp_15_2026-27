<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['A', 'B', 'C']),
            'year' => fake()->randomElement(['FY', 'SY', 'TY']),
            'department_id' => Department::factory(),
        ];
    }
}
