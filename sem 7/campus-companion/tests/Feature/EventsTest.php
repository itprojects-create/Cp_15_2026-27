<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_event()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'CMPN']);

        $this->actingAs($admin);

        Volt::test('admin.events.create')
            ->set('title', 'Hackathon 2026')
            ->set('description', 'Annual 24-hour hackathon in college campus')
            ->set('date', '2026-09-15')
            ->set('location', 'Auditorium')
            ->set('department_id', $dept->id)
            ->call('save')
            ->assertRedirect('/admin/events');

        $this->assertDatabaseHas('events', [
            'title' => 'Hackathon 2026',
            'location' => 'Auditorium',
            'created_by' => $admin->id,
            'department_id' => $dept->id,
        ]);
    }

    public function test_admin_can_edit_event()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'title' => 'Tech Talks',
            'description' => 'Guest lecture by industry expert',
            'date' => '2026-10-01',
            'location' => 'Seminar Hall 1',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Volt::test('admin.events.edit', ['id' => $event->id])
            ->set('title', 'Tech Talks 2026 (Updated)')
            ->set('location', 'Seminar Hall 2')
            ->call('update')
            ->assertRedirect('/admin/events');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Tech Talks 2026 (Updated)',
            'location' => 'Seminar Hall 2',
        ]);
    }

    public function test_admin_can_delete_event()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'title' => 'Cancelled Fest',
            'description' => 'Will not occur',
            'date' => '2026-11-01',
            'location' => 'Ground',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Volt::test('admin.events.index')
            ->call('delete', $event->id);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_teacher_can_view_department_and_global_events()
    {
        $dept = Department::create(['name' => 'IT']);
        $otherDept = Department::create(['name' => 'EXTC']);

        $teacher = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);
        $admin = User::factory()->create(['role' => 'admin']);

        $globalEvent = Event::create([
            'title' => 'Annual Day',
            'description' => 'All college fest',
            'date' => '2026-12-10',
            'location' => 'Main Ground',
            'department_id' => null,
            'created_by' => $admin->id,
        ]);

        $deptEvent = Event::create([
            'title' => 'IT Project Showcase',
            'description' => 'Showcase of final year projects',
            'date' => '2026-12-12',
            'location' => 'Lab 301',
            'department_id' => $dept->id,
            'created_by' => $admin->id,
        ]);

        $otherDeptEvent = Event::create([
            'title' => 'EXTC Robotics Workshop',
            'description' => 'Robotics for EXTC students',
            'date' => '2026-12-14',
            'location' => 'Lab 201',
            'department_id' => $otherDept->id,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($teacher);

        $test = Volt::test('teacher.events.index');
        $test->assertSee('Annual Day');
        $test->assertSee('IT Project Showcase');
        $test->assertDontSee('EXTC Robotics Workshop');
    }
}
