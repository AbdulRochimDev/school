<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{SchoolClass, Subject, ClassSubject, Enrollment, Term, Assignment, Submission, SubmissionFile, Student, Teacher};

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // AcademicYear (settings table or academic_years)
        DB::table('academic_years')->insertOrIgnore([
            'id' => 1,
            'name' => date('Y').'/'.(date('Y')+1),
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $term = Term::firstOrCreate(['name'=>'Term 1 '.date('Y')], [
            'start_date'=>now()->startOfYear(), 'end_date'=>now()->endOfYear(), 'active'=>true
        ]);

        $class = SchoolClass::firstOrCreate(['name'=>'Class A 1'], ['level'=>'1']);
        $subject = Subject::firstOrCreate(['code'=>'MTH101'], ['name'=>'Math']);

        $teacher = Teacher::firstOrCreate(['user_id'=>DB::table('users')->where('email','teacher@example.com')->value('id') ?: null], ['name'=>'Guru Contoh']);
        if (!$teacher->exists) { $teacher = Teacher::firstOrCreate(['name'=>'Guru Contoh']); }

        $cs = ClassSubject::firstOrCreate([
            'class_id'=>$class->id,'subject_id'=>$subject->id
        ], ['teacher_id'=>$teacher->id]);

        $student = Student::firstOrCreate(['user_id'=>DB::table('users')->where('email','student@example.com')->value('id') ?: null], ['name'=>'Siswa Contoh','class_id'=>$class->id]);
        Enrollment::firstOrCreate(['class_id'=>$class->id,'student_id'=>$student->id,'term_id'=>$term->id], ['enrolled_at'=>now()]);

        // Optional sample assignment + submission
        $assignment = Assignment::firstOrCreate(['class_subject_id'=>$cs->id,'title'=>'Tugas 1'], ['description'=>'Contoh tugas','due_at'=>now()->addWeek(),'max_score'=>100]);
        $submission = Submission::firstOrCreate(['assignment_id'=>$assignment->id,'student_id'=>$student->id], ['content'=>'Jawaban contoh','submitted_at'=>now()]);
        SubmissionFile::firstOrCreate(['submission_id'=>$submission->id,'file_path'=>'submissions/'.date('Y/m/d').'/sample.txt'], ['mime_type'=>'text/plain','size_bytes'=>123]);
    }
}

