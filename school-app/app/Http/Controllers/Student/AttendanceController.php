<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{AttendanceRecord};

class AttendanceController extends Controller
{
    public function summary()
    {
        $studentId = auth()->user()->student->id ?? null;
        abort_unless($studentId, 403);

        $summary = AttendanceRecord::query()
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END), 0) AS present")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END), 0) AS late")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END), 0) AS excused")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END), 0) AS absent")
            ->where('student_id', $studentId)
            ->first();

        return [
            'present' => (int) ($summary->present ?? 0),
            'late' => (int) ($summary->late ?? 0),
            'excused' => (int) ($summary->excused ?? 0),
            'absent' => (int) ($summary->absent ?? 0),
        ];
    }
}

