<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Floor;
use App\Models\FloorBlock;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CampusMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_switch_levels_and_select_rooms()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Volt::test('student.map')
            ->assertSet('selectedLevel', 1)
            ->assertSet('selectedRoomId', 'cr-102')
            ->call('selectRoom', 'cr-101')
            ->assertSet('selectedRoomId', 'cr-101')
            ->call('setLevel', 2)
            ->assertSet('selectedLevel', 2);
    }

    public function test_filter_blueprint_rooms_by_search_and_category()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Volt::test('student.map')
            ->set('search', 'Robotics')
            ->assertSet('search', 'Robotics')
            ->call('setCategory', 'classroom')
            ->assertSet('selectedCategory', 'classroom')
            ->call('selectRoom', 'cr-103')
            ->assertSet('selectedRoomId', 'cr-103');
    }

    public function test_switch_map_view_modes()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Volt::test('student.map')
            ->assertSet('viewMode', 'blueprint')
            ->set('viewMode', 'leaflet')
            ->assertSet('viewMode', 'leaflet')
            ->call('selectRoom', 'vesit-library')
            ->assertSet('selectedRoomId', 'vesit-library');
    }

    public function test_admin_and_scheduler_can_access_admin_map()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scheduler = User::factory()->create(['role' => 'timetable_commity']);

        $this->actingAs($admin);
        Volt::test('admin.map')
            ->assertSet('selectedLevel', 1)
            ->call('selectRoom', 'comp-lab-1')
            ->assertSet('selectedRoomId', 'comp-lab-1');

        $this->actingAs($scheduler);
        Volt::test('admin.map')
            ->call('selectRoom', 'faculty-office')
            ->assertSet('selectedRoomId', 'faculty-office');
    }

    public function test_floor_and_floor_blocks_database_models()
    {
        $dept = Department::create(['name' => 'Information Technology']);
        $floor = Floor::create([
            'name' => '1st Floor',
            'level' => 1,
            'department_id' => $dept->id,
        ]);

        $block = FloorBlock::create([
            'floor_id' => $floor->id,
            'block_number' => 'CR-101',
            'title' => 'Smart Lecture Hall CR-101',
            'x_grid' => 1,
            'y_grid' => 1,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'emerald',
        ]);

        $this->assertDatabaseHas('floors', [
            'id' => $floor->id,
            'name' => '1st Floor',
            'department_id' => $dept->id,
        ]);

        $this->assertDatabaseHas('floor_blocks', [
            'id' => $block->id,
            'block_number' => 'CR-101',
            'floor_id' => $floor->id,
        ]);

        $this->assertEquals($floor->id, $block->floor->id);
    }
}
