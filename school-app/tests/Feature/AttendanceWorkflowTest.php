<?php

namespace Tests\Feature;

use App\Events\AttendanceSessionClosed;
use App\Events\GradesUpdated;
use App\Models\{AttendanceRecord, AttendanceSession, ClassSubject, Enrollment, Grade, GradeItem, ReportCard, ReportCardItem, Role, SchoolClass, Student, Subject, Teacher, Term, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_attendance_summary_returns_aggregated_counts(): void
    {
        [$class, $subject, $teacher, $classSubject] = $this->createClassroom();

        $studentUser = User::create([
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'password' => Hash::make('secret'),
        ]);

        $studentRole = Role::firstOrCreate(['slug' => 'siswa'], ['name' => 'Siswa']);
        $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'name' => 'Student One',
        ]);

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
        ]);

        foreach (['present', 'late', 'excused', 'absent'] as $offset => $status) {
            $session = AttendanceSession::create([
                'class_id' => $class->id,
                'class_subject_id' => $classSubject->id,
                'teacher_id' => $teacher->id,
                'session_date' => now()->addDays($offset)->toDateString(),
                'status' => 'closed',
            ]);

            AttendanceRecord::create([
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => $status,
            ]);
        }

        $this->actingAs($studentUser);
        $this->assertSame(User::class, get_class(auth()->user()));
        $this->assertNotNull(auth()->user()->student);
        $this->assertTrue(auth()->user()->roles()->where('slug', 'siswa')->exists());
        $this->assertDatabaseHas('role_user', ['user_id' => $studentUser->id, 'role_id' => $studentRole->id]);
        $this->assertTrue(auth()->user()->hasRole('siswa'));
        $this->assertTrue(Gate::forUser(auth()->user())->allows('isStudent'));

        $response = $this->getJson('/api/v1/siswa/attendance');

        $response->assertOk()->assertExactJson([
            'present' => 1,
            'late' => 1,
            'excused' => 1,
            'absent' => 1,
        ]);
    }

    public function test_recompute_attendance_grades_aggregates_records_and_dispatches_event(): void
    {
        Event::fake([GradesUpdated::class]);

        [$class, $subject, $teacher, $classSubject] = $this->createClassroom();

        $studentUser = User::create([
            'name' => 'Student Two',
            'email' => 'student2@example.com',
            'password' => Hash::make('secret'),
        ]);

        $studentRole = Role::firstOrCreate(['slug' => 'siswa'], ['name' => 'Siswa']);
        $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'name' => 'Student Two',
        ]);

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
        ]);

        $sessionCurrent = AttendanceSession::create([
            'class_id' => $class->id,
            'class_subject_id' => $classSubject->id,
            'teacher_id' => $teacher->id,
            'session_date' => now()->toDateString(),
            'status' => 'closed',
        ]);

        $sessionPrevious = AttendanceSession::create([
            'class_id' => $class->id,
            'class_subject_id' => $classSubject->id,
            'teacher_id' => $teacher->id,
            'session_date' => now()->subDay()->toDateString(),
            'status' => 'closed',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $sessionCurrent->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $sessionPrevious->id,
            'student_id' => $student->id,
            'status' => 'late',
        ]);

        event(new AttendanceSessionClosed($sessionCurrent));

        $gradeItem = GradeItem::where('class_subject_id', $classSubject->id)->where('name', 'Attendance')->first();
        $this->assertNotNull($gradeItem);

        $grade = Grade::where('grade_item_id', $gradeItem->id)->where('student_id', $student->id)->first();
        $this->assertNotNull($grade);
        $this->assertEquals(75.0, (float) $grade->score);

        Event::assertDispatched(GradesUpdated::class, function (GradesUpdated $event) use ($student) {
            return $event->studentIds === [$student->id];
        });
    }

    public function test_update_report_cards_rolls_up_weighted_scores(): void
    {
        [$class, $subject, $teacher, $classSubject] = $this->createClassroom();

        $studentUser = User::create([
            'name' => 'Student Three',
            'email' => 'student3@example.com',
            'password' => Hash::make('secret'),
        ]);

        $studentRole = Role::firstOrCreate(['slug' => 'siswa'], ['name' => 'Siswa']);
        $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'name' => 'Student Three',
        ]);

        $term = Term::create([
            'name' => 'Term 1',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'active' => true,
        ]);

        $gradeItemHomework = GradeItem::create([
            'class_subject_id' => $classSubject->id,
            'name' => 'Homework',
            'weight' => 60,
            'max_score' => 100,
        ]);

        $gradeItemExam = GradeItem::create([
            'class_subject_id' => $classSubject->id,
            'name' => 'Exam',
            'weight' => 40,
            'max_score' => 100,
        ]);

        Grade::create([
            'grade_item_id' => $gradeItemHomework->id,
            'student_id' => $student->id,
            'score' => 80,
            'graded_at' => now(),
        ]);

        Grade::create([
            'grade_item_id' => $gradeItemExam->id,
            'student_id' => $student->id,
            'score' => 70,
            'graded_at' => now(),
        ]);

        event(new GradesUpdated([$student->id], $term->id));

        $reportCard = ReportCard::where('student_id', $student->id)->where('term_id', $term->id)->first();
        $this->assertNotNull($reportCard);
        $this->assertEquals(76.0, (float) $reportCard->final_score);

        $items = ReportCardItem::where('report_card_id', $reportCard->id)->get()->keyBy('grade_item_id');
        $this->assertCount(2, $items);
        $this->assertSame(60.0, (float) $items[$gradeItemHomework->id]->weight);
        $this->assertSame(80.0, (float) $items[$gradeItemHomework->id]->score);
        $this->assertSame(40.0, (float) $items[$gradeItemExam->id]->weight);
        $this->assertSame(70.0, (float) $items[$gradeItemExam->id]->score);
    }

    private function createClassroom(): array
    {
        $class = SchoolClass::create([
            'name' => 'Class A',
            'level' => '10',
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH',
        ]);

        $teacherUser = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('secret'),
        ]);

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'name' => 'Teacher',
            'nip' => 'T-001',
        ]);

        $classSubject = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        return [$class, $subject, $teacher, $classSubject];
    }
}
