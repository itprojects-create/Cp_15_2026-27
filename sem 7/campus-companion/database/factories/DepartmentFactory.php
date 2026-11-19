<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        static $departments = [
            'Computer Engineering',
            'Information Technology',
            'Electronics',
            'Artificial Intelligence',
            'Robotics'
        ];

        return [
            'name' => fake()->unique()->randomElement($departments),
        ];
    }
}
