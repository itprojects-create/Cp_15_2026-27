<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;
use App\Models\FloorBlock;
use App\Models\ClassRoom;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $classrooms = ClassRoom::all();

        $ground = Floor::create(['name' => 'Ground Floor', 'level' => 0]);
        $first = Floor::create(['name' => 'First Floor', 'level' => 1]);
        $second = Floor::create(['name' => 'Second Floor', 'level' => 2]);

        // Seed some sample blocks on Ground Floor
        FloorBlock::create([
            'floor_id' => $ground->id,
            'block_number' => '001',
            'title' => 'Main Auditorium',
            'class_room_id' => null,
            'x_grid' => 1,
            'y_grid' => 1,
            'width_grid' => 4,
            'height_grid' => 3,
            'color' => 'indigo'
        ]);

        FloorBlock::create([
            'floor_id' => $ground->id,
            'block_number' => '002',
            'title' => 'Chemistry Lab',
            'class_room_id' => $classrooms->first()?->id,
            'x_grid' => 6,
            'y_grid' => 1,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'emerald'
        ]);

        FloorBlock::create([
            'floor_id' => $ground->id,
            'block_number' => '003',
            'title' => 'Admin Office',
            'class_room_id' => null,
            'x_grid' => 10,
            'y_grid' => 1,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'rose'
        ]);

        // Seed some sample blocks on First Floor
        FloorBlock::create([
            'floor_id' => $first->id,
            'block_number' => '101',
            'title' => 'Classroom 101 (IT Division A)',
            'class_room_id' => $classrooms->skip(1)->first()?->id,
            'x_grid' => 2,
            'y_grid' => 2,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'blue'
        ]);

        FloorBlock::create([
            'floor_id' => $first->id,
            'block_number' => '102',
            'title' => 'Classroom 102 (IT Division B)',
            'class_room_id' => $classrooms->skip(2)->first()?->id,
            'x_grid' => 6,
            'y_grid' => 2,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'amber'
        ]);
    }
}
