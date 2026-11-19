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

    public function test_student_can_switch_floors_and_view_blocks()
    {
        $dept = Department::create(['name' => 'IT']);
        $class = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $dept->id]);
        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $dept->id,
            'class_room_id' => $class->id,
        ]);

        $floor1 = Floor::create(['name' => 'Ground Floor', 'level' => 0]);
        $floor2 = Floor::create(['name' => 'First Floor', 'level' => 1]);

        $block1 = FloorBlock::create([
            'floor_id' => $floor1->id,
            'block_number' => 'G-01',
            'title' => 'Main Library',
            'x_grid' => 1,
            'y_grid' => 1,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'blue',
        ]);

        $block2 = FloorBlock::create([
            'floor_id' => $floor2->id,
            'block_number' => '1-02',
            'title' => 'Class D10',
            'class_room_id' => $class->id,
            'x_grid' => 2,
            'y_grid' => 2,
            'width_grid' => 3,
            'height_grid' => 2,
            'color' => 'green',
        ]);

        $this->actingAs($student);

        Volt::test('student.map')
            ->assertSet('selectedFloorId', $floor1->id)
            ->set('selectedFloorId', $floor2->id)
            ->call('loadFloorBlocks')
            ->call('selectBlock', 0)
            ->assertSet('selectedBlock.title', 'Class D10');
    }

    public function test_filter_locations_by_search_and_category()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Volt::test('student.map')
            ->set('search', 'Library')
            ->assertSet('search', 'Library')
            ->call('selectLocation', 'library')
            ->assertSet('selectedLocationId', 'library');
    }

    public function test_admin_and_scheduler_can_create_floor_with_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scheduler = User::factory()->create(['role' => 'timetable_commity']);
        $dept = Department::create(['name' => 'IT Department']);

        $this->actingAs($admin);
        Volt::test('admin.map')
            ->set('newFloorName', '3rd Floor')
            ->set('newFloorDepartmentId', $dept->id)
            ->call('addFloor');

        $this->assertDatabaseHas('floors', [
            'name' => '3rd Floor',
            'department_id' => $dept->id,
        ]);

        $this->actingAs($scheduler);
        Volt::test('admin.map')
            ->set('newFloorName', '4th Floor')
            ->set('newFloorDepartmentId', $dept->id)
            ->call('addFloor');

        $this->assertDatabaseHas('floors', [
            'name' => '4th Floor',
            'department_id' => $dept->id,
        ]);
    }

    public function test_admin_can_edit_and_delete_floor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept1 = Department::create(['name' => 'Department A']);
        $dept2 = Department::create(['name' => 'Department B']);

        $floor = Floor::create([
            'name' => 'Floor X',
            'level' => 5,
            'department_id' => $dept1->id,
        ]);

        $this->actingAs($admin);
        Volt::test('admin.map')
            ->call('editFloor', $floor->id)
            ->assertSet('showFloorEditModal', true)
            ->assertSet('editingFloorName', 'Floor X')
            ->set('editingFloorName', 'Floor Y')
            ->set('editingFloorDepartmentId', $dept2->id)
            ->call('updateFloor')
            ->assertSet('showFloorEditModal', false);

        $this->assertDatabaseHas('floors', [
            'id' => $floor->id,
            'name' => 'Floor Y',
            'department_id' => $dept2->id,
        ]);

        Volt::test('admin.map')
            ->call('deleteFloor', $floor->id);

        $this->assertDatabaseMissing('floors', [
            'id' => $floor->id,
        ]);
    }
}
