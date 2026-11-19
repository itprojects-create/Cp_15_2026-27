<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        for ($i = 1; $i <= 10; $i++) {
            Event::create([
                'title' => "Event $i",
                'description' => "Event description $i",
                'date' => now()->addDays(rand(1,30)),
                'location' => "Hall " . rand(1,5),
                'created_by' => $admin->id,
            ]);
        }
    }
}
