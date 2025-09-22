<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeItem;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\LedgerEntry;
use App\Models\Notification;
use App\Models\PPDBApplication;
use App\Models\PPDBDocument;
use App\Models\Payment;
use App\Models\PaymentVerification;
use App\Models\ReportCard;
use App\Models\ReportCardItem;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();
            $roles = Role::pluck('id', 'slug');

            $accounts = [
                [
                    'name' => 'Admin Demo',
                    'email' => 'admin.demo@example.com',
                    'password' => 'password',
                    'roles' => ['admin'],
                ],
                [
                    'name' => 'Admin Akademik Demo',
                    'email' => 'akademik.demo@example.com',
                    'password' => 'password',
                    'roles' => ['admin_akademik'],
                ],
                [
                    'name' => 'Admin Keuangan Demo',
                    'email' => 'keuangan.demo@example.com',
                    'password' => 'password',
                    'roles' => ['admin_keuangan'],
                ],
                [
                    'name' => 'Operator PPDB Demo',
                    'email' => 'ppdb.demo@example.com',
                    'password' => 'password',
                    'roles' => ['operator_ppdb'],
                ],
                [
                    'name' => 'Guru Matematika Demo',
                    'email' => 'guru.demo@example.com',
                    'password' => 'password',
                    'roles' => ['guru'],
                    'teacher' => [
                        'nip' => '1987001',
                    ],
                ],
                [
                    'name' => 'Wali Kelas Demo',
                    'email' => 'walikelas.demo@example.com',
                    'password' => 'password',
                    'roles' => ['guru', 'wali_kelas'],
                    'teacher' => [
                        'nip' => '1987002',
                    ],
                ],
                [
                    'name' => 'Siswa Demo Utama',
                    'email' => 'siswa.demo@example.com',
                    'password' => 'password',
                    'roles' => ['siswa'],
                    'student' => [
                        'nis' => 'S2025001',
                        'nisn' => '2025000001',
                    ],
                ],
                [
                    'name' => 'Siswa Demo Dua',
                    'email' => 'siswa2.demo@example.com',
                    'password' => 'password',
                    'roles' => ['siswa'],
                    'student' => [
                        'nis' => 'S2025002',
                        'nisn' => '2025000002',
                    ],
                ],
                [
                    'name' => 'Siswa Demo Tiga',
                    'email' => 'siswa3.demo@example.com',
                    'password' => 'password',
                    'roles' => ['siswa'],
                    'student' => [
                        'nis' => 'S2025003',
                        'nisn' => '2025000003',
                    ],
                ],
            ];

            $teachers = [];
            $students = [];

            foreach ($accounts as $account) {
                $user = User::updateOrCreate(
                    ['email' => $account['email']],
                    [
                        'name' => $account['name'],
                        'password' => Hash::make($account['password']),
                    ]
                );

                foreach ($account['roles'] as $slug) {
                    $roleId = $roles[$slug] ?? null;
                    if ($roleId) {
                        DB::table('role_user')->insertOrIgnore([
                            'role_id' => $roleId,
                            'user_id' => $user->id,
                        ]);
                    }
                }

                if (isset($account['teacher'])) {
                    $teachers[] = Teacher::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'name' => $account['name'],
                            'nip' => Arr::get($account['teacher'], 'nip'),
                        ]
                    );
                }

                if (isset($account['student'])) {
                    $students[] = Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'name' => $account['name'],
                            'nis' => Arr::get($account['student'], 'nis'),
                            'nisn' => Arr::get($account['student'], 'nisn'),
                        ]
                    );
                }
            }

            $homeroom = $teachers[1] ?? $teachers[0] ?? null;
            $subjectTeacher = $teachers[0] ?? $homeroom;

            $class = SchoolClass::updateOrCreate(
                ['name' => 'Kelas 7A'],
                [
                    'level' => '7',
                    'homeroom_teacher_id' => $homeroom?->id,
                ]
            );

            $term = Term::updateOrCreate(
                ['name' => 'Semester Ganjil '.$now->year],
                [
                    'start_date' => $now->copy()->startOfYear(),
                    'end_date' => $now->copy()->endOfYear(),
                    'active' => true,
                ]
            );

            $subjects = [
                'MATH7' => Subject::updateOrCreate(['code' => 'MATH7'], ['name' => 'Matematika Dasar']),
                'SCI7' => Subject::updateOrCreate(['code' => 'SCI7'], ['name' => 'IPA Terpadu']),
            ];

            $classSubjects = [
                ClassSubject::updateOrCreate(
                    ['class_id' => $class->id, 'subject_id' => $subjects['MATH7']->id],
                    ['teacher_id' => $subjectTeacher?->id]
                ),
                ClassSubject::updateOrCreate(
                    ['class_id' => $class->id, 'subject_id' => $subjects['SCI7']->id],
                    ['teacher_id' => $homeroom?->id ?? $subjectTeacher?->id]
                ),
            ];

            foreach ($students as $student) {
                $student->update(['class_id' => $class->id]);
                Enrollment::updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'student_id' => $student->id,
                        'term_id' => $term->id,
                    ],
                    ['enrolled_at' => $now]
                );
            }

            $session = AttendanceSession::updateOrCreate(
                [
                    'class_subject_id' => $classSubjects[0]->id,
                    'session_date' => $now->toDateString(),
                ],
                [
                    'term_id' => $term->id,
                    'class_id' => $class->id,
                    'teacher_id' => $subjectTeacher?->id ?? $homeroom?->id,
                    'starts_at' => $now->copy()->setTime(7, 30),
                    'ends_at' => $now->copy()->setTime(8, 30),
                    'status' => 'closed',
                    'topic' => 'Pengantar Aljabar',
                ]
            );

            foreach ($students as $index => $student) {
                $status = match ($index) {
                    0 => 'present',
                    1 => 'late',
                    default => 'present',
                };

                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $status,
                        'checkin_at' => $now->copy()->setTime(7, 25 + $index * 5),
                        'note' => $status === 'late' ? 'Datang terlambat karena hujan' : null,
                    ]
                );
            }

            $assignment = Assignment::updateOrCreate(
                [
                    'class_subject_id' => $classSubjects[0]->id,
                    'title' => 'Tugas Aljabar 1',
                ],
                [
                    'description' => 'Kerjakan latihan transformasi persamaan linear.',
                    'due_at' => $now->copy()->addWeek(),
                    'max_score' => 100,
                ]
            );

            $gradeItem = GradeItem::updateOrCreate(
                [
                    'class_subject_id' => $classSubjects[0]->id,
                    'name' => 'Penilaian Harian Aljabar',
                ],
                [
                    'weight' => 0.4,
                    'max_score' => 100,
                ]
            );

            $gradeScores = [];
            foreach ($students as $index => $student) {
                $score = 85 + ($index * 5);
                $gradeScores[$student->id] = $score;

                $submission = Submission::updateOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'content' => 'Jawaban tugas '.$student->name,
                        'submitted_at' => $now->copy()->addDays(1 + $index),
                        'score' => $score,
                        'feedback' => $score >= 90 ? 'Pertahankan!' : 'Perbaiki langkah penyelesaian.',
                    ]
                );

                SubmissionFile::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'file_path' => 'submissions/'.$now->format('Y/m/d').'/'.$student->id.'_aljabar.pdf',
                    ],
                    [
                        'mime_type' => 'application/pdf',
                        'size_bytes' => 204800,
                    ]
                );

                Grade::updateOrCreate(
                    [
                        'grade_item_id' => $gradeItem->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'score' => $score,
                        'graded_at' => $now,
                    ]
                );

                $reportCard = ReportCard::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'term_id' => $term->id,
                    ],
                    [
                        'final_score' => round($score * 0.95, 2),
                        'published_at' => $now->copy()->addWeeks(6),
                    ]
                );

                ReportCardItem::updateOrCreate(
                    [
                        'report_card_id' => $reportCard->id,
                        'grade_item_id' => $gradeItem->id,
                    ],
                    [
                        'score' => $score,
                        'weight' => 0.4,
                    ]
                );
            }

            $primaryStudent = $students[0] ?? null;
            if ($primaryStudent) {
                $invoice = Invoice::updateOrCreate(
                    ['number' => 'INV-'.$now->format('Ym').'-001'],
                    [
                        'student_id' => $primaryStudent->id,
                        'amount' => 500000,
                        'status' => 'paid',
                        'due_date' => $now->copy()->addWeeks(2)->toDateString(),
                        'issued_at' => $now,
                    ]
                );

                $payment = Payment::updateOrCreate(
                    ['invoice_id' => $invoice->id, 'reference' => 'PAY-'.$now->format('Ymd').'01'],
                    [
                        'amount' => 500000,
                        'method' => 'transfer',
                        'paid_at' => $now->copy()->addDay(),
                    ]
                );

                PaymentVerification::updateOrCreate(
                    ['payment_id' => $payment->id],
                    [
                        'verified_by' => $roles['admin_keuangan'] ? DB::table('role_user')->where('role_id', $roles['admin_keuangan'])->value('user_id') : null,
                        'verified_at' => $now->copy()->addDays(2),
                        'status' => 'approved',
                        'note' => 'Pembayaran dikonfirmasi otomatis.',
                    ]
                );

                $ledger = Ledger::updateOrCreate(
                    ['code' => 'TUITION'],
                    ['name' => 'Pembayaran SPP']
                );

                LedgerEntry::updateOrCreate(
                    ['reference' => $payment->reference ?? 'PAY-'.$payment->id],
                    [
                        'ledger_id' => $ledger->id,
                        'entry_date' => $now->toDateString(),
                        'type' => 'credit',
                        'amount' => 500000,
                        'note' => 'SPP '.$primaryStudent->name,
                    ]
                );

                Notification::updateOrCreate(
                    [
                        'user_id' => $primaryStudent->user_id,
                        'type' => 'finance.invoice.paid',
                    ],
                    [
                        'data' => json_encode([
                            'invoice' => $invoice->number,
                            'amount' => 500000,
                            'paid_at' => $payment->paid_at,
                        ]),
                        'read_at' => null,
                    ]
                );

                ActivityLog::updateOrCreate(
                    [
                        'user_id' => $primaryStudent->user_id,
                        'action' => 'invoice_paid',
                        'subject_type' => Invoice::class,
                        'subject_id' => $invoice->id,
                    ],
                    [
                        'properties' => json_encode([
                            'payment_reference' => $payment->reference,
                            'amount' => 500000,
                        ]),
                        'created_at' => $now,
                    ]
                );
            }

            Setting::updateOrCreate(['key' => 'school.name'], ['value' => 'Sekolah Demo Laravel']);
            Setting::updateOrCreate(['key' => 'school.year'], ['value' => $now->year.' / '.($now->year + 1)]);

            $applicantUser = User::updateOrCreate(
                ['email' => 'applicant.demo@example.com'],
                [
                    'name' => 'Calon Siswa Demo',
                    'password' => Hash::make('password'),
                ]
            );
            $roles->has('siswa') && DB::table('role_user')->insertOrIgnore([
                'role_id' => $roles['siswa'] ?? null,
                'user_id' => $applicantUser->id,
            ]);

            $application = PPDBApplication::updateOrCreate(
                ['user_id' => $applicantUser->id],
                [
                    'status' => 'verified',
                    'submitted_at' => $now->copy()->subDays(3),
                ]
            );

            PPDBDocument::updateOrCreate(
                ['ppdb_application_id' => $application->id, 'type' => 'ijazah'],
                [
                    'file_path' => 'ppdb/documents/ijazah_applicant.pdf',
                    'verified_at' => $now->copy()->subDays(1),
                ]
            );
        });
    }
}
