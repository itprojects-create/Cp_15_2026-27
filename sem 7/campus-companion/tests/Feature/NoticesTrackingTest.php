<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notice;
use App\Models\NoticeRead;
use App\Models\Notification;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class NoticesTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_notice_and_students_are_notified()
    {
        $department = Department::create(['name' => 'IT Department']);
        $classroom = ClassRoom::create(['name' => 'SY IT', 'year' => 'SY', 'department_id' => $department->id]);

        $teacher = User::factory()->create([
            'role' => 'teacher',
            'department_id' => $department->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'class_room_id' => $classroom->id,
        ]);

        $this->actingAs($teacher);

        Volt::test('teacher.notices.create')
            ->set('title', 'Exam Schedule')
            ->set('description', 'Exam details description here')
            ->set('category', 'academic')
            ->set('class_room_id', $classroom->id)
            ->call('save');

        $this->assertDatabaseHas('notices', [
            'title' => 'Exam Schedule',
            'category' => 'academic',
            'created_by' => $teacher->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'message' => "New Notice: 'Exam Schedule' posted by {$teacher->name}",
        ]);
    }

    public function test_student_viewing_notice_logs_read_receipt()
    {
        $student = User::factory()->create(['role' => 'student']);
        $notice = Notice::create([
            'title' => 'Important Update',
            'description' => 'Detailed update information',
            'category' => 'general',
            'created_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);

        $this->actingAs($student);

        Volt::test('student.notices.index')
            ->call('openNotice', $notice->id);

        $this->assertDatabaseHas('notice_reads', [
            'user_id' => $student->id,
            'notice_id' => $notice->id,
        ]);
    }

    public function test_reminders_command_sends_notifications_to_unseen_students()
    {
        $department = Department::create(['name' => 'IT Department']);
        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        // Notice created 3 days ago
        $notice = Notice::create([
            'title' => 'Forgotten Assignment',
            'description' => 'Please submit it',
            'category' => 'academic',
            'department_id' => $department->id,
            'created_by' => $admin->id,
            'created_at' => now()->subDays(3),
        ]);

        $this->artisan('reminders:send')
            ->expectsOutput('Starting unseen reminders dispatcher...')
            ->expectsOutput('Completed! Dispatched 1 reminder notifications.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'message' => "Reminder: You have an unread notice: 'Forgotten Assignment'",
        ]);
    }

    public function test_admin_can_create_scoped_notice_and_student_only_sees_their_scope()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $itDept = Department::create(['name' => 'IT Department']);
        $cmpnDept = Department::create(['name' => 'CMPN Department']);

        $itClass = ClassRoom::create(['name' => 'D10', 'year' => 'SY', 'department_id' => $itDept->id]);
        $cmpnClass = ClassRoom::create(['name' => 'D7', 'year' => 'SY', 'department_id' => $cmpnDept->id]);

        $itStudent = User::factory()->create([
            'role' => 'student',
            'department_id' => $itDept->id,
            'class_room_id' => $itClass->id,
        ]);

        $this->actingAs($admin);

        // 1. Admin creates notice scoped to CMPN class
        Volt::test('admin.notices.create')
            ->set('title', 'CMPN Only Notice')
            ->set('description', 'Only for CMPN class D7')
            ->set('category', 'academic')
            ->set('department_id', $cmpnDept->id)
            ->set('class_room_id', $cmpnClass->id)
            ->call('save')
            ->assertRedirect('/admin/notices');

        // 2. Admin creates global notice
        Volt::test('admin.notices.create')
            ->set('title', 'Campus Holiday Notice')
            ->set('description', 'College is closed tomorrow')
            ->set('category', 'general')
            ->set('department_id', '')
            ->set('class_room_id', '')
            ->call('save')
            ->assertRedirect('/admin/notices');

        // 3. IT Student visits student notices
        $this->actingAs($itStudent);

        Volt::test('student.notices.index')
            ->assertSee('Campus Holiday Notice')
            ->assertDontSee('CMPN Only Notice');
    }

    public function test_user_can_mark_notifications_as_read()
    {
        $user = User::factory()->create(['role' => 'student']);
        $n1 = Notification::create([
            'user_id' => $user->id,
            'message' => 'Notification 1',
            'read' => false,
        ]);
        $n2 = Notification::create([
            'user_id' => $user->id,
            'message' => 'Notification 2',
            'read' => false,
        ]);

        $this->actingAs($user)
            ->postJson('/notifications/mark-as-read')
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', [
            'id' => $n1->id,
            'read' => true,
        ]);
        $this->assertDatabaseHas('notifications', [
            'id' => $n2->id,
            'read' => true,
        ]);
    }
}
