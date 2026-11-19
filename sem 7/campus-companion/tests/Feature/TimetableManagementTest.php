<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class TimetableManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_timetable_slot()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Information Technology']);
        $class = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $dept->id]);
        $teacher = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);

        $this->actingAs($admin);

        Volt::test('admin.timetable.create')
            ->set('class_room_id', $class->id)
            ->set('day', 'Monday')
            ->set('time_start', '08:30')
            ->set('time_end', '09:30')
            ->set('subject', 'Data Structures')
            ->set('faculty_id', $teacher->id)
            ->set('room', 'Lab 301')
            ->call('save')
            ->assertRedirect('/admin/timetable');

        $this->assertDatabaseHas('timetables', [
            'class_room_id' => $class->id,
            'day' => 'Monday',
            'time_slot' => '08:30-09:30',
            'subject' => 'Data Structures',
            'faculty_id' => $teacher->id,
            'room' => 'Lab 301',
        ]);
    }

    public function test_conflict_detection_prevents_overlapping_class_slots()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Computer Engineering']);
        $class = ClassRoom::create(['name' => 'D15', 'year' => 'TY', 'department_id' => $dept->id]);
        $teacher1 = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);
        $teacher2 = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);

        Timetable::create([
            'class_room_id' => $class->id,
            'day' => 'Monday',
            'time_slot' => '09:30-10:30',
            'subject' => 'Operating Systems',
            'faculty_id' => $teacher1->id,
            'room' => 'Room 401',
        ]);

        $this->actingAs($admin);

        Volt::test('admin.timetable.create')
            ->set('class_room_id', $class->id)
            ->set('day', 'Monday')
            ->set('time_start', '09:30')
            ->set('time_end', '10:30')
            ->set('subject', 'Computer Networks')
            ->set('faculty_id', $teacher2->id)
            ->set('room', 'Room 402')
            ->call('save')
            ->assertHasErrors(['conflict']);
    }

    public function test_find_clash_alternatives_excludes_clashing_class()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Computer Engineering']);
        $classA = ClassRoom::create(['name' => 'D7A', 'year' => 'SY', 'department_id' => $dept->id]);
        $classB = ClassRoom::create(['name' => 'D7B', 'year' => 'SY', 'department_id' => $dept->id]);

        $this->actingAs($admin);

        $test = Volt::test('admin.timetable.index')
            ->set('clashDay', 'Monday')
            ->set('clashTimeSlot', '08:30-09:30')
            ->set('clashingClassId', (string) $classA->id)
            ->call('findClashAlternatives');

        $alternatives = $test->get('clashAlternatives');
        $altIds = collect($alternatives)->pluck('id')->all();

        // Class A itself must not be suggested as an alternative for Class A
        $this->assertNotContains($classA->id, $altIds);
        // Class B (which is free) should be suggested
        $this->assertContains($classB->id, $altIds);
    }

    public function test_conflict_detection_detects_afternoon_12hr_and_24hr_overlap()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = Department::create(['name' => 'Information Technology']);
        $class = ClassRoom::create(['name' => 'D10A', 'year' => 'SY', 'department_id' => $dept->id]);
        $teacher = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);
        $teacher2 = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);

        // Stored in 12-hour afternoon format
        Timetable::create([
            'class_room_id' => $class->id,
            'day' => 'Tuesday',
            'time_slot' => '01:30-02:30',
            'subject' => 'Web Technologies',
            'faculty_id' => $teacher->id,
            'room' => 'Lab 501',
        ]);

        $this->actingAs($admin);

        // Attempting to create lecture in 24-hour format: 13:30 to 14:30
        Volt::test('admin.timetable.create')
            ->set('class_room_id', $class->id)
            ->set('day', 'Tuesday')
            ->set('time_start', '13:30')
            ->set('time_end', '14:30')
            ->set('subject', 'Cloud Computing')
            ->set('faculty_id', $teacher2->id)
            ->set('room', 'Lab 502')
            ->call('save')
            ->assertHasErrors(['conflict']);
    }
}
