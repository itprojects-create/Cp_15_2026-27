<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Timetable;
use App\Models\User;
use App\Services\TimetableImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class MasterTimetableImportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $timetableCommittee;
    protected $teacher;
    protected $student;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create([
            'name' => 'Information Technology',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'department_id' => $this->department->id,
        ]);

        $this->timetableCommittee = User::factory()->create([
            'role' => 'timetable_commity',
            'department_id' => $this->department->id,
        ]);

        $this->teacher = User::factory()->create([
            'name' => 'Nitin C N',
            'role' => 'teacher',
            'department_id' => $this->department->id,
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'department_id' => $this->department->id,
        ]);
    }

    public function test_admin_and_timetable_committee_can_access_master_timetable_and_import_pages()
    {
        // Admin access
        $this->actingAs($this->admin)
            ->get('/admin/timetable/master')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/timetable/import')
            ->assertStatus(200);

        // Timetable Committee access
        $this->actingAs($this->timetableCommittee)
            ->get('/admin/timetable/master')
            ->assertStatus(200);

        $this->actingAs($this->timetableCommittee)
            ->get('/admin/timetable/import')
            ->assertStatus(200);
    }

    public function test_students_and_teachers_are_forbidden_from_master_import()
    {
        $this->actingAs($this->student)
            ->get('/admin/timetable/import')
            ->assertStatus(403);

        $this->actingAs($this->teacher)
            ->get('/admin/timetable/import')
            ->assertStatus(403);
    }

    public function test_service_standardizes_time_slots()
    {
        $service = new TimetableImportService();

        $this->assertEquals('08:30-09:30', $service->standardizeTimeSlot('8:30-9:30'));
        $this->assertEquals('08:30-09:30', $service->standardizeTimeSlot('08:30–09:30'));
        $this->assertEquals('09:30-10:30', $service->standardizeTimeSlot('9:30 to 10:30'));
        $this->assertEquals('12:30-01:30', $service->standardizeTimeSlot('12:30-1:30'));
    }

    public function test_service_imports_lectures_and_creates_classes_and_timetables()
    {
        $service = new TimetableImportService();

        $dummyLectures = [
            [
                'day' => 'Monday',
                'time_slot' => '08:30-09:30',
                'class_name' => 'D7A',
                'class_year' => 'D7',
                'class_division' => 'A',
                'subject' => 'Data Structures',
                'faculty_id' => $this->teacher->id,
                'faculty_initials' => 'NCN',
                'faculty_name' => $this->teacher->name,
                'room' => '312',
                'is_break' => false,
                'raw_content' => 'Data Structures (NCN) 312',
            ],
            [
                'day' => 'Monday',
                'time_slot' => '09:30-10:30',
                'class_name' => 'D7A',
                'class_year' => 'D7',
                'class_division' => 'A',
                'subject' => 'DBMS',
                'faculty_id' => $this->teacher->id,
                'faculty_initials' => 'NCN',
                'faculty_name' => $this->teacher->name,
                'room' => '312',
                'is_break' => false,
                'raw_content' => 'DBMS (NCN) 312',
            ],
            [
                'day' => 'Monday',
                'time_slot' => '08:30-09:30',
                'class_name' => 'D10B',
                'class_year' => 'D10',
                'class_division' => 'B',
                'subject' => 'DevOps',
                'faculty_id' => $this->teacher->id,
                'faculty_initials' => 'NCN',
                'faculty_name' => $this->teacher->name,
                'room' => '515',
                'is_break' => false,
                'raw_content' => 'DevOps (NCN) 515',
            ],
        ];

        $result = $service->importLectures($this->department->id, $dummyLectures, true);

        $this->assertEquals(3, $result['total_inserted']);
        $this->assertEquals(2, $result['total_classes']);

        // Assert ClassRooms were created in DB
        $this->assertDatabaseHas('class_rooms', [
            'department_id' => $this->department->id,
            'year' => 'D7',
            'name' => 'A',
        ]);
        $this->assertDatabaseHas('class_rooms', [
            'department_id' => $this->department->id,
            'year' => 'D10',
            'name' => 'B',
        ]);

        // Assert Timetables were created in DB
        $this->assertDatabaseHas('timetables', [
            'day' => 'Monday',
            'time_slot' => '08:30-09:30',
            'subject' => 'Data Structures',
            'room' => '312',
            'faculty_id' => $this->teacher->id,
        ]);
        $this->assertDatabaseHas('timetables', [
            'day' => 'Monday',
            'time_slot' => '08:30-09:30',
            'subject' => 'DevOps',
            'room' => '515',
            'faculty_id' => $this->teacher->id,
        ]);
    }

    public function test_master_grid_livewire_component_renders_department_schedule()
    {
        $class = ClassRoom::create([
            'department_id' => $this->department->id,
            'name' => 'A',
            'year' => 'D7',
        ]);

        Timetable::create([
            'day' => 'Monday',
            'time_slot' => '08:30-09:30',
            'subject' => 'Software Engineering',
            'room' => '312',
            'class_room_id' => $class->id,
            'faculty_id' => $this->teacher->id,
        ]);

        $this->actingAs($this->admin);

        Volt::test('admin.timetable.master', ['department_id' => $this->department->id])
            ->assertSee('Department Master Timetable')
            ->assertSee('Software Engineering')
            ->assertSee('D7 A')
            ->assertSee('Rm 312');
    }

    public function test_import_component_can_download_template()
    {
        $this->actingAs($this->admin);

        Volt::test('admin.timetable.import')
            ->call('downloadTemplate')
            ->assertFileDownloaded('master_timetable_template.csv');
    }
}
