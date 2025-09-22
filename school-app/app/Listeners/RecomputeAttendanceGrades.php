<?php
namespace App\Listeners;

use App\Events\{AttendanceSessionClosed, GradesUpdated};
use App\Models\{AttendanceRecord, AttendanceSession, Enrollment, Grade, GradeItem};
use App\Services\AttendanceScoreService;
use Illuminate\Support\Facades\DB;

class RecomputeAttendanceGrades
{
    public function handle(AttendanceSessionClosed $event): void
    {
        $session = $event->session;
        $classSubjectId = $session->class_subject_id;
        if (!$classSubjectId) {
            return;
        }

        $classId = $session->class_id ?: optional($session->classSubject)->class_id;
        if (!$classId) {
            return;
        }

        $closedSessionIds = AttendanceSession::query()
            ->where('class_subject_id', $classSubjectId)
            ->where('status', 'closed')
            ->pluck('id');
        $totalSessions = $closedSessionIds->count();
        if ($totalSessions === 0) {
            return;
        }

        $studentIds = Enrollment::where('class_id', $classId)
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->values();
        if ($studentIds->isEmpty()) {
            return;
        }

        $aggregated = AttendanceRecord::query()
            ->select('student_id')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_count")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late_count")
            ->selectRaw("SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) AS excused_count")
            ->whereIn('attendance_session_id', $closedSessionIds)
            ->whereIn('student_id', $studentIds)
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $scoreService = app(AttendanceScoreService::class);

        $updatedStudents = DB::transaction(function () use ($studentIds, $aggregated, $scoreService, $totalSessions, $classSubjectId) {
            $gradeItem = GradeItem::firstOrCreate(
                ['class_subject_id' => $classSubjectId, 'name' => 'Attendance'],
                ['weight' => 10, 'max_score' => 100]
            );

            $updated = [];

            foreach ($studentIds as $studentId) {
                $stats = $aggregated->get($studentId);
                $present = (int) ($stats->present_count ?? 0);
                $late = (int) ($stats->late_count ?? 0);
                $excused = (int) ($stats->excused_count ?? 0);

                $score = $scoreService->calculate($present, $late, $excused, $totalSessions);

                Grade::updateOrCreate(
                    ['grade_item_id' => $gradeItem->id, 'student_id' => $studentId],
                    ['score' => $score, 'graded_at' => now()]
                );

                $updated[] = $studentId;
            }

            return array_values(array_unique($updated));
        });

        if (!empty($updatedStudents)) {
            event(new GradesUpdated($updatedStudents, $session->term_id));
        }
    }
}
