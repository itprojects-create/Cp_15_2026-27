<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\Complaint;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ComplaintsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_complaint()
    {
        $dept = Department::create(['name' => 'IT']);
        $hod = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);
        $dept->update(['head_id' => $hod->id]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $dept->id,
        ]);

        $this->actingAs($student);

        Volt::test('student.complaints.index')
            ->set('title', 'Projector Malfunction')
            ->set('description', 'Projector in Lab 302 HDMI port is broken')
            ->set('type', 'lab')
            ->call('submit');

        $this->assertDatabaseHas('complaints', [
            'user_id' => $student->id,
            'title' => 'Projector Malfunction',
            'type' => 'lab',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $hod->id,
        ]);
    }

    public function test_teacher_can_resolve_department_complaint()
    {
        $dept = Department::create(['name' => 'IT']);
        $teacher = User::factory()->create(['role' => 'teacher', 'department_id' => $dept->id]);
        $student = User::factory()->create(['role' => 'student', 'department_id' => $dept->id]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'title' => 'Broken Chair',
            'description' => 'Room 401 chair broken',
            'type' => 'class',
            'status' => 'pending',
        ]);

        $this->actingAs($teacher);

        Volt::test('teacher.complaints.index')
            ->call('resolve', $complaint->id);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'message' => "Your complaint 'Broken Chair' has been resolved",
        ]);
    }

    public function test_admin_can_assign_teacher_to_complaint()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'title' => 'AC Not Cooling',
            'description' => 'Lab 101 AC issue',
            'type' => 'lab',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        Volt::test('admin.complaints.index')
            ->call('assign', $complaint->id, $teacher->id);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'assigned_to' => $teacher->id,
            'status' => 'in_progress',
        ]);
    }
}
