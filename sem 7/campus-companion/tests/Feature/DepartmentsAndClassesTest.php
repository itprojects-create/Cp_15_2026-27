<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class DepartmentsAndClassesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Volt::test('admin.departments.create')
            ->set('name', 'Artificial Intelligence & Data Science')
            ->call('save')
            ->assertRedirect('/admin/departments');

        $this->assertDatabaseHas('departments', [
            'name' => 'Artificial Intelligence & Data Science',
        ]);
    }

    public function test_admin_cannot_create_duplicate_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Department::create(['name' => 'Information Technology']);

        $this->actingAs($admin);

        Volt::test('admin.departments.create')
            ->set('name', 'Information Technology')
            ->call('save')
            ->assertHasErrors(['name' => 'unique']);
    }

    public function test_admin_can_update_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Computers']);

        $this->actingAs($admin);

        Volt::test('admin.departments.edit', ['id' => $dept->id])
            ->set('name', 'Computer Engineering')
            ->call('update')
            ->assertRedirect('/admin/departments');

        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
            'name' => 'Computer Engineering',
        ]);
    }

    public function test_admin_can_delete_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Electronics']);

        $this->actingAs($admin);

        Volt::test('admin.departments.index')
            ->call('delete', $dept->id);

        $this->assertDatabaseMissing('departments', [
            'id' => $dept->id,
        ]);
    }

    public function test_admin_can_view_classes_and_filter_by_department()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept1 = Department::create(['name' => 'IT']);
        $dept2 = Department::create(['name' => 'CMPN']);

        $class1 = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $dept1->id]);
        $class2 = ClassRoom::create(['name' => 'D15', 'year' => 'TY', 'department_id' => $dept2->id]);

        $this->actingAs($admin);

        Volt::test('admin.classes.index')
            ->set('selectedDepartment', $dept1->id)
            ->call('selectClass', $class1->id)
            ->assertSet('selectedClass', $class1->id);
    }

    public function test_admin_and_scheduler_can_create_class()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scheduler = User::factory()->create(['role' => 'timetable_commity']);
        $dept = Department::create(['name' => 'IT']);

        $this->actingAs($admin);
        Volt::test('admin.classes.create')
            ->set('year', 'FY')
            ->set('name', 'Div A')
            ->set('department_id', $dept->id)
            ->call('save')
            ->assertRedirect('/admin/classes');

        $this->assertDatabaseHas('class_rooms', [
            'year' => 'FY',
            'name' => 'Div A',
            'department_id' => $dept->id,
        ]);

        $this->actingAs($scheduler);
        Volt::test('admin.classes.create')
            ->set('year', 'SY')
            ->set('name', 'Div B')
            ->set('department_id', $dept->id)
            ->call('save')
            ->assertRedirect('/admin/classes');

        $this->assertDatabaseHas('class_rooms', [
            'year' => 'SY',
            'name' => 'Div B',
            'department_id' => $dept->id,
        ]);
    }

    public function test_admin_can_update_class()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Computers']);
        $class = ClassRoom::create(['name' => 'Old Div', 'year' => 'FY', 'department_id' => $dept->id]);

        $this->actingAs($admin);
        Volt::test('admin.classes.edit', ['id' => $class->id])
            ->set('year', 'TY')
            ->set('name', 'New Div')
            ->call('update')
            ->assertRedirect('/admin/classes');

        $this->assertDatabaseHas('class_rooms', [
            'id' => $class->id,
            'year' => 'TY',
            'name' => 'New Div',
        ]);
    }

    public function test_admin_can_delete_class()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'IT']);
        $class = ClassRoom::create(['name' => 'Temp Div', 'year' => 'SY', 'department_id' => $dept->id]);

        $this->actingAs($admin);
        Volt::test('admin.classes.index')
            ->call('deleteClass', $class->id);

        $this->assertDatabaseMissing('class_rooms', [
            'id' => $class->id,
        ]);
    }
}
