<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        for ($i = 1; $i <= 10; $i++) {
            Notice::create([
                'title' => "Notice $i",
                'description' => "This is notice description $i",
                'category' => ['Academic', 'Administrative'][rand(0,1)],
                'created_by' => $admin->id,
                'created_at' => now(),
            ]);
        }
    }
}
