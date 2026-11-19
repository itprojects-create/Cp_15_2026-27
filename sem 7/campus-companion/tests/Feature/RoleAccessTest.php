<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_protected_routes()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/teacher/dashboard')->assertRedirect('/login');
        $this->get('/student/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_redirects_user_to_appropriate_home()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin/dashboard');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher)->get('/dashboard')->assertRedirect('/teacher/dashboard');

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->get('/dashboard')->assertRedirect('/student/dashboard');
    }

    public function test_student_cannot_access_admin_or_teacher_routes()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        $this->get('/admin/dashboard')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/departments')->assertForbidden();
        $this->get('/teacher/dashboard')->assertForbidden();
    }

    public function test_teacher_cannot_access_admin_routes()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);

        $this->get('/admin/dashboard')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard_and_modules()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertSuccessful();
        $this->get('/admin/users')->assertSuccessful();
        $this->get('/admin/departments')->assertSuccessful();
        $this->get('/admin/classes')->assertSuccessful();
        $this->get('/admin/map')->assertSuccessful();
        $this->get('/admin/notices')->assertSuccessful();
        $this->get('/admin/events')->assertSuccessful();
        $this->get('/admin/timetable')->assertSuccessful();
        $this->get('/admin/complaints')->assertSuccessful();
    }

    public function test_teacher_can_access_teacher_portal_modules()
    {
        $dept = Department::create(['name' => 'Computer Engineering']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'department_id' => $dept->id,
        ]);
        $this->actingAs($teacher);

        $this->get('/teacher/dashboard')->assertSuccessful();
        $this->get('/teacher/notices')->assertSuccessful();
        $this->get('/teacher/notices/create')->assertSuccessful();
        $this->get('/teacher/events')->assertSuccessful();
        $this->get('/teacher/timetable')->assertSuccessful();
        $this->get('/teacher/complaints')->assertSuccessful();
        $this->get('/teacher/map')->assertSuccessful();
        $this->get('/teacher/profile')->assertSuccessful();
    }

    public function test_student_can_access_student_portal_modules()
    {
        $dept = Department::create(['name' => 'Information Technology']);
        $class = ClassRoom::create(['name' => 'TY IT', 'year' => 'TY', 'department_id' => $dept->id]);
        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $dept->id,
            'class_room_id' => $class->id,
        ]);
        $this->actingAs($student);

        $this->get('/student/dashboard')->assertSuccessful();
        $this->get('/student/notices')->assertSuccessful();
        $this->get('/student/events')->assertSuccessful();
        $this->get('/student/timetable')->assertSuccessful();
        $this->get('/student/complaints')->assertSuccessful();
        $this->get('/student/map')->assertSuccessful();
        $this->get('/student/profile')->assertSuccessful();
    }
}
