<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role','student')->pluck('id');

        foreach (range(1, 20) as $i) {
            Complaint::create([
                'user_id' => $students->random(),
                'title' => "Complaint $i",
                'description' => "Issue description $i",
                'type' => ['lab','class'][rand(0,1)],
                'status' => ['pending','in_progress','resolved'][rand(0,2)],
            ]);
        }
    }
}
