<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student_user()
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Information Technology']);
        $class = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $dept->id]);

        $this->actingAs($admin);

        Volt::test('admin.users.create')
            ->set('name', 'Rohan Sharma')
            ->set('email', 'rohan.sharma@ves.ac.in')
            ->set('role', 'student')
            ->set('department_id', $dept->id)
            ->set('class_room_id', $class->id)
            ->call('save')
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'name' => 'Rohan Sharma',
            'email' => 'rohan.sharma@ves.ac.in',
            'role' => 'student',
            'department_id' => $dept->id,
            'class_room_id' => $class->id,
        ]);
    }

    public function test_admin_can_filter_and_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userToDelete = User::factory()->create([
            'name' => 'Temporary User',
            'email' => 'temp@ves.ac.in',
            'role' => 'student',
        ]);

        $this->actingAs($admin);

        Volt::test('admin.users.index')
            ->set('search', 'Temporary')
            ->call('delete', $userToDelete->id);

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }

    public function test_admin_cannot_delete_teacher_with_active_timetable()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Information Technology']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'department_id' => $dept->id,
        ]);
        $class = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $dept->id]);

        \App\Models\Timetable::create([
            'day' => 'Monday',
            'time_slot' => '08:30-09:30',
            'subject' => 'Data Structures',
            'room' => '312',
            'class_room_id' => $class->id,
            'faculty_id' => $teacher->id,
        ]);

        $this->actingAs($admin);

        Volt::test('admin.users.index')
            ->call('delete', $teacher->id)
            ->assertSee('Cannot delete faculty');

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
        ]);
    }
}
