<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::where('role', 'student')->inRandomOrder()->first()?->id ?? 1,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['lab', 'class']),
            'status' => fake()->randomElement(['pending', 'in_progress', 'resolved']),
        ];
    }
}
